@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-white dark:border-oro text-start text-base font-bold text-white bg-[#0a1f1a] dark:bg-gray-800 transition duration-150 ease-in-out uppercase tracking-wider'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-[#0a1f1a] dark:hover:bg-gray-800 transition duration-150 ease-in-out uppercase tracking-wider';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
