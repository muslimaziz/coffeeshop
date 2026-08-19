@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'nama',
    'help' => null,
    'placeholder' => null,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-label-bold uppercase tracking-wider text-on-surface-variant">
            {{ $label }} @if ($required)<span class="text-error">*</span>@endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3"
            @required($required)
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" @required($required)
            class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest py-2.5 pl-4 pr-10 text-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
            <option value="">— Pilih —</option>
            @foreach ($options as $key => $option)
                @php
                    $optValue = $key;
                    $optLabel = $option;

                    if (is_array($option)) {
                        $optValue = $option[$optionValue] ?? $key;
                        $optLabel = $option[$optionLabel] ?? $option;
                    } elseif (is_object($option)) {
                        $optValue = $option->{$optionValue} ?? $key;
                        $optLabel = $option->{$optionLabel} ?? $option;
                    }

                    $selected = old($name, $value) == $optValue;
                @endphp
                <option value="{{ $optValue }}" @selected($selected)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif ($type === 'checkbox')
        <input type="hidden" name="{{ $name }}" value="0">
        <label class="flex cursor-pointer items-center gap-2">
            <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $value ? 1 : 0))
                class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary">
            <span class="text-body-sm text-on-surface-variant">Aktif</span>
        </label>
    @else
        <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            @required($required)
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
    @endif

    @if ($help)
        <p class="mt-1 text-body-sm text-on-surface-variant">{{ $help }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-body-sm text-error">{{ $message }}</p>
    @enderror
</div>