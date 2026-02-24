@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-oro focus:ring-oro rounded-md shadow-sm']) }}>
