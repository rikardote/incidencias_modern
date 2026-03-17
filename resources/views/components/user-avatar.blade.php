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

// Determine text size based on container size
$textSizeClass = str_contains($size, 'w-6') ? 'text-xs' :
                 (str_contains($size, 'w-12') ? 'text-2xl' :
                 (str_contains($size, 'w-16') ? 'text-4xl' : 'text-xl'));

// Initial state for PHP rendering
$initialSelected = $avatar && array_key_exists($avatar, $avatars) ? $avatars[$avatar] : null;
$initialInitial = strtoupper(mb_substr($name, 0, 1));
@endphp

<div x-data="{ 
    avatar: '{{ $avatar }}', 
    avatars: {{ json_encode($avatars) }},
    nameInitial: '{{ $initialInitial }}',
    get selected() { return this.avatars[this.avatar] || null }
}" 
x-on:avatar-updated.window="if($event.detail.avatar) this.avatar = $event.detail.avatar"
class="relative inline-block {{ $size }} flex-shrink-0">
    
    {{-- Render Emoji if selected --}}
    <template x-if="selected">
        <div class="flex items-center justify-center rounded-full {{ $size }} select-none transition-all duration-300"
            :class="selected.bg">
            <span class="{{ $textSizeClass }} leading-none" x-text="selected.emoji"></span>
        </div>
    </template>

    {{-- Render Initials if not selected --}}
    <template x-if="!selected">
        <div class="flex items-center justify-center rounded-full bg-gradient-to-br from-oro to-[#9b2247] text-white font-bold {{ $size }}">
            <span class="{{ $textSizeClass }} leading-none" x-text="nameInitial"></span>
        </div>
    </template>

    {{-- Static fallback for SSR or when JS is disabled --}}
    <noscript>
        @if($initialSelected)
            <div class="flex items-center justify-center rounded-full {{ $initialSelected['bg'] }} {{ $size }} flex-shrink-0 select-none">
                <span class="{{ $textSizeClass }} leading-none">{{ $initialSelected['emoji'] }}</span>
            </div>
        @else
            <div class="flex items-center justify-center rounded-full bg-gradient-to-br from-oro to-[#9b2247] text-white font-bold {{ $size }} flex-shrink-0">
                <span class="{{ $textSizeClass }} leading-none">{{ $initialInitial }}</span>
            </div>
        @endif
    </noscript>
</div>