<?php

namespace App\Http\Requests\Admin;

use App\Enums\ListingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVesselRequest extends FormRequest
{
    public function rules(): array
    {
        $vesselId = $this->route('vessel')?->id;

        return [
            'boat_operator_id' => ['nullable', Rule::exists('boat_operators', 'id')],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:20', Rule::unique('vessels', 'code')->ignore($vesselId)],
            'type' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:400'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'top_speed_knots' => ['nullable', 'integer', 'min:1', 'max:80'],
            'publish' => ['nullable', Rule::in(['publish', 'draft'])],
            'status' => ['required', Rule::enum(ListingStatus::class)],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:60'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['image', 'max:4096'],
            'testimonials' => ['nullable', 'array', 'max:20'],
            'testimonials.*.id' => ['nullable', 'integer'],
            'testimonials.*.name' => ['nullable', 'string', 'max:120'],
            'testimonials.*.stars' => ['nullable', 'integer', 'min:1', 'max:5'],
            'testimonials.*.quote' => ['nullable', 'string', 'max:600'],
            'testimonials.*.experienced_at' => ['nullable', 'date_format:Y-m'],
            'testimonials.*.remove' => ['nullable', 'boolean'],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => ['string'],
        ];
    }
}
