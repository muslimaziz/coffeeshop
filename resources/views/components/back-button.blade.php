@props(['href' => '#', 'label' => 'Kembali'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-xl border border-outline-variant/50 px-4 py-2 text-body-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface']) }}>
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    {{ $label }}
</a>
