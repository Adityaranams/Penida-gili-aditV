<?php

namespace App\Http\Requests\Admin;

use App\Enums\ListingStatus;
use App\Models\Schedule;
use App\Models\Vessel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    /**
     * The Figma form exposes a single "Route Segment" ("fromId-toId") and an
     * "Assigned Vessel"; the ports, operator and status are derived from those.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('route') && preg_match('/^(\d+)-(\d+)$/', (string) $this->input('route'), $m)) {
            $merge['from_port_id'] = (int) $m[1];
            $merge['to_port_id'] = (int) $m[2];
        }

        if ($this->filled('vessel_id') && ! $this->filled('boat_operator_id')) {
            $merge['boat_operator_id'] = Vessel::query()->whereKey($this->integer('vessel_id'))->value('boat_operator_id');
        }

        if ($this->filled('publish')) {
            $merge['status'] = $this->input('publish') === 'draft' ? ListingStatus::Draft->value : ListingStatus::Active->value;
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'route' => ['nullable', 'string', 'regex:/^\d+-\d+$/'],
            'publish' => ['nullable', Rule::in(['publish', 'draft'])],
            'boat_operator_id' => ['required', Rule::exists('boat_operators', 'id')],
            'vessel_id' => ['required', Rule::exists('vessels', 'id')->where('boat_operator_id', $this->integer('boat_operator_id'))],
            'from_port_id' => ['required', Rule::exists('ports', 'id')],
            'to_port_id' => ['required', 'different:from_port_id', Rule::exists('ports', 'id')],
            'departure_time' => ['required', 'date_format:H:i'],
            'arrival_time' => ['required', 'date_format:H:i', 'after:departure_time'],
            'price_adult' => ['required', 'integer', 'min:0'],
            'price_child' => ['required', 'integer', 'min:0'],
            'price_foreign' => ['nullable', 'integer', 'min:0'],
            'price_foreign_child' => ['nullable', 'integer', 'min:0'],
            'days' => ['nullable', 'array'],
            'days.*' => [Rule::in(Schedule::DAYS)],
            'status' => ['required', Rule::enum(ListingStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'from_port_id.required' => 'Choose a route segment.',
            'to_port_id.different' => 'Arrival port must differ from the departure port.',
            'arrival_time.after' => 'Arrival must be later than departure.',
        ];
    }

    /**
     * Validated attributes ready for the model. Selecting every day is the
     * same as "daily", so it is stored as null to keep the search simple.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = collect($this->validated())->except(['route', 'publish'])->all();
        $days = array_values(array_unique($data['days'] ?? []));
        $data['days'] = ($days === [] || count($days) === count(Schedule::DAYS)) ? null : $days;
        $data['price_foreign'] = $data['price_foreign'] ?? null;
        $data['price_foreign_child'] = $data['price_foreign_child'] ?? null;

        return $data;
    }
}
