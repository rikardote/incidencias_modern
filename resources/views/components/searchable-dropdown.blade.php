@props([
    'label',
    'placeholder',
    'wireModel',
    'items' => [],
    'selectedId' => null,
    'selectedName' => '',
    'itemClass',
    'color' => 'rose',
    'onSelect' => '',
    'selectedIdVar' => '',
    'selectedNameVar' => '',
    'highlightedIndex' => 0
])

@php
    // Clases completas para que Tailwind no las purgue
    $highlightClasses = match($color) {
        'cyan' => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600',
        'rose' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-600',
        default => 'bg-gray-50 dark:bg-gray-500/10 text-gray-600',
    };
    $normalClasses = 'text-gray-700 dark:text-gray-200';
    $selectedBg = match($color) {
        'cyan' => 'bg-cyan-50 dark:bg-cyan-500/10 border-cyan-200 dark:border-cyan-500/30',
        'rose' => 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/30',
        default => 'bg-gray-50 dark:bg-gray-500/10 border-gray-200 dark:border-gray-500/30',
    };
    $selectedText = match($color) {
        'cyan' => 'text-cyan-700 dark:text-cyan-400',
        'rose' => 'text-rose-700 dark:text-rose-400',
        default => 'text-gray-700 dark:text-gray-400',
    };
    $labelColor = match($color) {
        'cyan' => 'text-cyan-500',
        'rose' => 'text-rose-500',
        default => 'text-gray-500',
    };
    $focusBorder = match($color) {
        'cyan' => 'focus:border-cyan-400',
        'rose' => 'focus:border-rose-400',
        default => 'focus:border-gray-400',
    };
    $borderColor = match($color) {
        'cyan' => 'border-cyan-100 dark:border-gray-700',
        'rose' => 'border-rose-100 dark:border-gray-700',
        default => 'border-gray-100 dark:border-gray-700',
    };
    $clearBtnColor = match($color) {
        'cyan' => 'text-cyan-400',
        'rose' => 'text-rose-400',
        default => 'text-gray-400',
    };
@endphp

<div class="relative" wire:key="{{ $wireModel }}-container" wire:ignore.self x-data="{ open: false }">
    <label class="block text-[8px] font-black {{ $labelColor }} uppercase mb-1 ml-1 tracking-widest">{{ $label }}</label>
    
    @if($selectedId)
        <div class="h-8 px-3 {{ $selectedBg }} border rounded-xl flex items-center justify-between">
            <span class="text-[9px] font-black {{ $selectedText }} uppercase truncate">{{ $selectedName }}</span>
            <button wire:click="$set('{{ $selectedIdVar }}', null); $set('{{ $selectedNameVar }}', '')" class="{{ $clearBtnColor }} hover:text-rose-500 font-black">×</button>
        </div>
    @else
        <input type="text" wire:model.live="{{ $wireModel }}" placeholder="{{ $placeholder }}" 
            autocomplete="off"
            @focus="open = true" @click.away="open = false" 
            wire:keydown.arrow-down.prevent="incrementHighlight"
            wire:keydown.arrow-up.prevent="decrementHighlight"
            wire:keydown.enter.prevent="selectHighlighted"
            class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-bold outline-none {{ $focusBorder }} transition-all uppercase">
        
        @if(count($items) > 0)
            <div x-show="open" class="absolute left-0 right-0 top-14 z-50 bg-white dark:bg-gray-800 shadow-2xl rounded-xl border {{ $borderColor }} overflow-hidden ring-4 ring-black/5 max-h-48 overflow-y-auto">
                @foreach($items as $idx => $item)
                    @php $displayName = $item['fullname'] ?? $item['name']; @endphp
                    <button wire:key="{{ $itemClass }}-btn-{{ $item['id'] }}" 
                        wire:click="{{ $onSelect }}('{{ $item['id'] }}', '{{ addslashes($displayName) }}')" 
                        @click="open = false"
                        class="{{ $itemClass }} w-full px-3 py-2 text-left border-b border-gray-50 dark:border-gray-700 last:border-0 transition-colors {{ $highlightedIndex == $idx ? $highlightClasses : $normalClasses }}"
                        @mouseenter="$wire.set('highlightedIndex', {{ $idx }})"
                        @if($highlightedIndex == $idx) x-init="$el.scrollIntoView({ block: 'nearest' })" @endif>
                        <div class="text-[9px] font-black uppercase truncate">{{ $displayName }}</div>
                    </button>
                @endforeach
            </div>
        @endif
    @endif
</div>
