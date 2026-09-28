<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Vessel;
use App\Support\BookingReport;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin dashboard — Figma node 1:6642.
     */
    public function index(Request $request): View
    {
        $thisMonth = Booking::query()->where('created_at', '>=', now()->startOfMonth());
        $lastMonth = Booking::query()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);

        $bookingsNow = (clone $thisMonth)->count();
        $bookingsPrev = (clone $lastMonth)->count();
        $revenueNow = (int) (clone $thisMonth)->where('status', BookingStatus::Confirmed)->sum('total');
        $revenuePrev = (int) (clone $lastMonth)->where('status', BookingStatus::Confirmed)->sum('total');

        $activeVessels = Vessel::query()->active()->count();
        $totalVessels = Vessel::query()->count();

        // Same query as the Booking Report, so both pages always agree.
        $transactions = BookingReport::filtered($request)->paginate(10)->withQueryString();

        return view('admin.dashboard', [
            'kpis' => [
                ['icon' => 'kpi-bookings.svg', 'label' => 'Total Bookings', 'value' => number_format(Booking::query()->count()), 'badge' => $this->delta($bookingsNow, $bookingsPrev), 'badgeTone' => 'up'],
                ['icon' => 'kpi-revenue.svg', 'label' => 'Total Revenue', 'value' => Money::idr((int) Booking::query()->where('status', BookingStatus::Confirmed)->sum('total'), 'Rp'), 'badge' => $this->delta($revenueNow, $revenuePrev), 'badgeTone' => 'up'],
                ['icon' => 'kpi-boat.svg', 'label' => 'Active Boat', 'value' => "{$activeVessels}/{$totalVessels}", 'badge' => $activeVessels === $totalVessels ? 'Operational' : ($totalVessels - $activeVessels).' offline', 'badgeTone' => 'neutral'],
            ],
            'vessels' => $this->fleetStatus(),
            'transactions' => $transactions->getCollection()->map->toReportRow(),
            'transactionsPaginator' => $transactions,
            'transactionsFilters' => $request->only(['q', 'type']),
            'transactionsSummary' => $transactions->total() === 0
                ? 'No transactions match this filter'
                : 'Showing '.$transactions->firstItem().'–'.$transactions->lastItem().' of '.number_format($transactions->total()).' entries',
        ]);
    }

    /** "+12%" style month-over-month change. */
    private function delta(int $now, int $prev): string
    {
        if ($prev === 0) {
            return $now > 0 ? 'New' : '0%';
        }

        $pct = (($now - $prev) / $prev) * 100;

        return sprintf('%s%s%%', $pct >= 0 ? '+' : '', number_format($pct, 1));
    }

    /**
     * Live fleet cards: derive "in transit" from the active schedules around the current time.
     *
     * @return list<array<string, mixed>>
     */
    private function fleetStatus(): array
    {
        $now = now();

        return Vessel::query()->active()->with(['operator', 'schedules' => fn ($q) => $q->active()->with(['fromPort', 'toPort'])])
            ->take(3)
            ->get()
            ->map(function (Vessel $vessel) use ($now) {
                $current = $vessel->schedules->first(function (Schedule $s) use ($now) {
                    $dep = Carbon::parse($s->departure_time)->setDateFrom($now);
                    $arr = Carbon::parse($s->arrival_time)->setDateFrom($now);

                    return $now->between($dep, $arr);
                });

                $next = $vessel->schedules->first(fn (Schedule $s) => Carbon::parse($s->departure_time)->setDateFrom($now)->gt($now))
                    ?? $vessel->schedules->first();

                if ($current) {
                    $dep = Carbon::parse($current->departure_time)->setDateFrom($now);
                    $arr = Carbon::parse($current->arrival_time)->setDateFrom($now);
                    $progress = (int) round($dep->diffInMinutes($now) / max(1, $dep->diffInMinutes($arr)) * 100);

                    return [
                        'name' => $vessel->name, 'status' => 'In Transit', 'state' => 'transit', 'progress' => min(99, $progress),
                        'eta' => 'ETA: '.$now->diffInMinutes($arr).' mins',
                        'meta' => [
                            ['icon' => 'route.svg', 'label' => $current->fromPort->name.' - '.$current->toPort->name],
                            ['icon' => 'speed.svg', 'label' => ($vessel->top_speed_knots ?? 24).' knots'],
                        ],
                    ];
                }

                return [
                    'name' => $vessel->name, 'status' => 'Docked', 'state' => 'docked', 'progress' => 100,
                    'eta' => $next ? 'Departure: '.$next->departure_label : 'No schedule',
                    'meta' => [
                        ['icon' => 'anchor.svg', 'label' => ($next?->fromPort->name ?? 'Harbour').' Port'],
                        ['icon' => 'boarding.svg', 'label' => $next ? 'Boarding' : 'Standby'],
                    ],
                ];
            })
            ->all();
    }
}
