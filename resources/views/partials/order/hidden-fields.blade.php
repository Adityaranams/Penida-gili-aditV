{{-- Product + date context carried from the order page into the booking POST. --}}
@props(['order'])

@isset($order['schedule_id'])
    <input type="hidden" name="schedule_id" value="{{ $order['schedule_id'] }}">
@endisset
@isset($order['room_id'])
    <input type="hidden" name="room_id" value="{{ $order['room_id'] }}">
@endisset
@isset($order['rooms'])
    {{-- Only when the form has no visible Rooms stepper; otherwise that field posts it
         and a second input of the same name would fight with it. --}}
    @unless (collect($order['party'] ?? [])->contains('name', 'rooms'))
        <input type="hidden" name="rooms" value="{{ $order['rooms'] }}">
    @endunless
@endisset
<input type="hidden" name="travel_date" value="{{ $order['travel_date'] }}">
@if (! empty($order['check_out']))
    <input type="hidden" name="check_out" value="{{ $order['check_out'] }}">
@endif
