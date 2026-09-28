<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vessel;
use App\Support\BookingReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Booking Report — Figma node 1:10402.
     */
    public function index(Request $request): View
    {
        $bookings = BookingReport::filtered($request)->paginate(10)->withQueryString();

        return view('admin.report', [
            'bookings' => $bookings,
            'rows' => $bookings->getCollection()->map->toReportRow(),
            'filters' => $request->only(['q', 'vessel', 'status', 'date']),
            'vessels' => Vessel::query()->orderBy('name')->pluck('name', 'id')->all(),
            'statuses' => BookingStatus::cases(),
        ]);
    }

    /** Change a reservation's status from the report row menu. */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(BookingStatus::class)]]);

        match (BookingStatus::from($data['status'])) {
            BookingStatus::Confirmed => $booking->confirm(),
            BookingStatus::Cancelled => $booking->cancel(),
            BookingStatus::Pending => $booking->forceFill(['status' => BookingStatus::Pending, 'confirmed_at' => null])->save(),
        };

        return back()->with('flash', "Booking {$booking->reference} marked ".$booking->status->label().'.');
    }

    /** CSV export of the current filter. */
    public function export(Request $request): StreamedResponse
    {
        $query = BookingReport::filtered($request);
        $filename = 'bookings-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Customer', 'Email', 'Phone', 'Nationality', 'Product', 'Travel date', 'Adults', 'Children', 'Total (IDR)', 'Status', 'Payment', 'Booked at']);

            $query->lazyById(200)->each(function (Booking $b) use ($out): void {
                fputcsv($out, [
                    $b->reference, $b->customer_name, $b->customer_email, $b->dial_code.' '.$b->phone, $b->nationality,
                    $b->product_label, $b->travel_date->toDateString(), $b->adults, $b->children, $b->total,
                    $b->status->label(), $b->payment_status->label(), $b->created_at->toDateTimeString(),
                ]);
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
