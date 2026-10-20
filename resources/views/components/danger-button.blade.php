<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors font-medium text-sm']) }}>
    {{ $slot }}
</button>
