<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Models\Port;
use App\Models\Schedule;
use App\Models\Vessel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /** Schedule listing — Figma node 1:9017. */
    public function index(Request $request): View
    {
        // "Sanur to Nusa Penida" pins each end; a lone term may match either port.
        [$from, $to] = array_pad(preg_split('/\s+(?:to|-|→|>)\s+/iu', $request->string('q')->trim()->value(), 2), 2, null);
        $date = $request->date('date');

        $schedules = Schedule::query()
            ->with(['operator', 'vessel', 'fromPort', 'toPort'])
            ->when(filled($from) && filled($to), fn ($q) => $q->betweenPorts($from, $to))
            ->when(filled($from) && blank($to), fn ($q) => $q->where(fn ($w) => $w
                ->betweenPorts($from, null)
                ->orWhere(fn ($x) => $x->betweenPorts(null, $from))))
            ->when($request->filled('vessel'), fn ($q) => $q->where('vessel_id', $request->integer('vessel')))
            // Schedules with no days run daily; otherwise the chosen date's weekday must be listed.
            ->when($date, fn ($q) => $q->where(fn ($w) => $w
                ->whereNull('days')
                ->orWhereJsonContains('days', $date->format('D'))))
            ->orderBy('departure_time')
            ->paginate(10)
            ->withQueryString();

        return view('admin.schedules', [
            'schedules' => $schedules,
            'filters' => $request->only(['q', 'vessel', 'date']),
            'vessels' => Vessel::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /** Add New Schedule — Figma node 1:7269. */
    public function create(): View
    {
        return $this->form(new Schedule(['status' => ListingStatus::Draft]));
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $schedule = Schedule::query()->create($request->payload());

        return redirect()->route('admin.schedules')->with('flash', 'Schedule '.$schedule->route_label.' saved.');
    }

    public function edit(Schedule $schedule): View
    {
        return $this->form($schedule);
    }

    public function update(StoreScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->payload());

        return redirect()->route('admin.schedules')->with('flash', 'Schedule '.$schedule->route_label.' updated.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules')->with('flash', 'Schedule removed.');
    }

    /** Delete everything ticked in the listing. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->collect('ids')->filter()->all();
        $removed = $ids ? Schedule::query()->whereKey($ids)->delete() : 0;

        return back()->with('flash', $removed
            ? $removed.' '.str('schedule')->plural($removed).' removed.'
            : 'Nothing was selected.');
    }

    private function form(Schedule $schedule): View
    {
        $vessels = Vessel::query()->with('operator')->orderBy('name')->get();

        return view('admin.schedules-create', [
            'schedule' => $schedule,
            'routes' => $this->establishedRoutes($schedule),
            'vesselOptions' => $vessels->mapWithKeys(fn (Vessel $v) => [$v->id => $v->name.' (Cap '.$v->capacity.')'])->all(),
            'vesselCapacities' => $vessels->pluck('capacity', 'id')->all(),
            'days' => Schedule::DAYS,
            'publishModes' => [
                ['value' => 'publish', 'label' => 'Publish Immediately', 'description' => 'Live to all passenger channels right away'],
                ['value' => 'draft', 'label' => 'Save as Draft', 'description' => 'Internal review without public URL'],
            ],
        ]);
    }

    /**
     * "Route Segment" choices: every port pair that already has a schedule, plus the
     * pair being edited. Values are "fromId-toId"; the request splits them again.
     *
     * @return array<string, string>
     */
    private function establishedRoutes(Schedule $schedule): array
    {
        $pairs = Schedule::query()
            ->with(['fromPort', 'toPort'])
            ->get(['from_port_id', 'to_port_id'])
            ->when($schedule->from_port_id && $schedule->to_port_id, fn ($c) => $c->push($schedule))
            ->unique(fn (Schedule $s) => $s->from_port_id.'-'.$s->to_port_id);

        if ($pairs->isEmpty()) {
            // Nothing scheduled yet: offer every ordered port pair so the first route can be created.
            $ports = Port::query()->orderBy('name')->get();

            return $ports->flatMap(fn (Port $from) => $ports
                ->reject(fn (Port $to) => $to->is($from))
                ->mapWithKeys(fn (Port $to) => [$from->id.'-'.$to->id => $from->name.' → '.$to->name]))->all();
        }

        return $pairs
            ->sortBy(fn (Schedule $s) => $s->fromPort->name.$s->toPort->name)
            ->mapWithKeys(fn (Schedule $s) => [$s->from_port_id.'-'.$s->to_port_id => $s->fromPort->name.' → '.$s->toPort->name])
            ->all();
    }
}
