{{-- Bulk delete for a console listing.

     The <form> lives outside the table (and outside the filter form) and the row
     checkboxes join it through their `form` attribute, so no forms are nested.
     The button only appears once something is ticked — see resources/js/bulk-select.js. --}}
@props(['action', 'noun' => 'rows'])

<form id="bulk-delete" method="post" action="{{ $action }}" class="hidden" data-bulk-form data-bulk-noun="{{ $noun }}">
    @csrf
    @method('DELETE')
</form>
