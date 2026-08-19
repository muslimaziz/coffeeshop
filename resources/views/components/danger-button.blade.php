<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-error border border-transparent rounded-lg font-semibold text-xs text-on-error uppercase tracking-widest hover:bg-error-container hover:text-on-error-container focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>