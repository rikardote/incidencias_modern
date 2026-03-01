@props(['avatar' => null, 'name' => '', 'size' => 'w-10 h-10', 'iconSize' => 'w-6 h-6'])

@php
$avatars = [
'man' => ['emoji' => '👨', 'bg' => 'bg-blue-100 dark:bg-blue-900'],
'woman' => ['emoji' => '👩', 'bg' => 'bg-pink-100 dark:bg-pink-900'],
'dog' => ['emoji' => '🐶', 'bg' => 'bg-orange-100 dark:bg-orange-900'],
'cat' => ['emoji' => '🐱', 'bg' => 'bg-teal-100 dark:bg-teal-900'],
'bird' => ['emoji' => '🐦', 'bg' => 'bg-green-100 dark:bg-green-900'],
'guitar' => ['emoji' => '🎸', 'bg' => 'bg-purple-100 dark:bg-purple-900'],
'drum' => ['emoji' => '🥁', 'bg' => 'bg-amber-100 dark:bg-amber-900'],
'palette' => ['emoji' => '🎨', 'bg' => 'bg-rose-100 dark:bg-rose-900'],
'robot' => ['emoji' => '🤖', 'bg' => 'bg-cyan-100 dark:bg-cyan-900'],
'alien' => ['emoji' => '👽', 'bg' => 'bg-indigo-100 dark:bg-indigo-900'],
];

$selected = $avatar && array_key_exists($avatar, $avatars) ? $avatars[$avatar] : null;

// Scale emoji font proportional to the container size
$textSize = str_contains($size, 'w-6') ? 'text-xs' :
(str_contains($size, 'w-12') ? 'text-2xl' :
(str_contains($size, 'w-16') ? 'text-4xl' : 'text-xl'));
@endphp

@if($selected)
<div class="flex items-center justify-center rounded-full {{ $selected['bg'] }} {{ $size }} flex-shrink-0 select-none">
    <span class="{{ $textSize }} leading-none transform translate-y-[2px]">{{ $selected['emoji'] }}</span>
</div>
@else
<div
    class="flex items-center justify-center rounded-full bg-gradient-to-br from-oro to-[#9b2247] text-white font-bold {{ $size }} flex-shrink-0">
    <span class="{{ $textSize }} leading-none">{{ strtoupper(mb_substr($name, 0, 1)) }}</span>
</div>
@endif