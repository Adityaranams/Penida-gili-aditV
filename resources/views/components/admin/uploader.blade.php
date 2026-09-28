{{-- Dashed drop zone (Figma 1:7212). A real file input sits behind the label so the
     control works without any JavaScript; resources/js/uploader.js adds drag-and-drop,
     thumbnails and per-file removal.

     Pass the photos already on the record as the default slot: they are hidden as soon
     as the admin picks replacements, so the form always shows what will be saved. --}}
@props(['name' => 'photos', 'hint' => 'SVG, PNG, JPG or GIF (MAX. 800x400px)', 'multiple' => true])

<div data-uploader>
    <label class="flex cursor-pointer flex-col items-center justify-center rounded-admin border-2 border-dashed border-[rgba(192,199,211,0.6)] bg-[#f7fafc] p-[34px]
                  transition-colors duration-300 hover:border-editorial hover:bg-[#eef4f8]
                  data-[dragging=true]:border-editorial data-[dragging=true]:bg-[#eef4f8]"
           data-uploader-drop>
        <input type="file" name="{{ $multiple ? $name.'[]' : $name }}" accept="image/*" @if ($multiple) multiple @endif
               class="sr-only" data-uploader-input>

        <span class="mb-[16px] flex size-[64px] items-center justify-center rounded-full bg-[#d5e2e9]">
            <img src="{{ asset('images/icons/admin/form-upload.svg') }}" alt="" class="size-[20px]">
        </span>

        <span class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">Click to upload or drag and drop</span>
        <span class="font-jakarta text-[14px] leading-[20px] text-editorial-body">{{ $hint }}</span>
    </label>

    {{-- Thumbnails of the current selection; filled by the script, hidden until something is picked. --}}
    <div class="mt-[16px] grid gap-[16px] sm:grid-cols-2" data-uploader-preview hidden></div>

    @if (trim($slot) !== '')
        <div data-uploader-current>
            {{ $slot }}
        </div>
    @endif

    @error($name)
        <p class="mt-[8px] font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</p>
    @enderror
    @error($name.'.*')
        <p class="mt-[8px] font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</p>
    @enderror
</div>
