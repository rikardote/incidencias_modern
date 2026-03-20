<div
    x-data="{
        open: false,
        hasNew: false,
        flash() {
            this.hasNew = true;
            setTimeout(() => this.hasNew = false, 600);
        }
    }"
    class="relative"
>
    {{-- ── BOTÓN CAMPANA ── --}}
    <button
        @click="open = !open"
        class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition active:scale-95"
        title="Notificaciones"
    >
        <svg class="w-5 h-5 transition-transform" :class="hasNew ? 'animate-wiggle' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[9px] text-white font-black items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        </span>
        @endif
    </button>

    {{-- ── DROPDOWN ── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        @click.outside="open = false"
        x-cloak
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden origin-top-right"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Notificaciones</span>
            @if($unreadCount > 0)
            <button wire:click="markAllRead" class="text-[10px] text-[#9b2247] dark:text-[#e6d194] hover:underline font-semibold transition">
                Marcar todo como leído
            </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($notifications as $n)
            @php
                $leftBorder = [
                    'info'    => 'border-l-blue-500',
                    'success' => 'border-l-emerald-500',
                    'warning' => 'border-l-yellow-400',
                    'danger'  => 'border-l-red-500',
                ][$n->type] ?? 'border-l-blue-500';

                $iconPath = [
                    'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                    'danger'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                ][$n->type] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';

                $iconColor = [
                    'info'    => 'text-blue-500',
                    'success' => 'text-emerald-500',
                    'warning' => 'text-yellow-500',
                    'danger'  => 'text-red-500',
                ][$n->type] ?? 'text-blue-500';
            @endphp
            <div
                wire:key="notif-{{ $n->id }}"
                class="flex gap-3 px-4 py-3 border-l-4 {{ $leftBorder }} transition hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer {{ $n->is_read ? 'opacity-50' : '' }}"
                wire:click="markAsRead({{ $n->id }})"
            >
                <div class="shrink-0 mt-0.5">
                    <svg class="w-4 h-4 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-[12px] font-bold text-gray-800 dark:text-gray-100 leading-tight">{{ $n->title }}</p>
                        @if(!$n->is_read)
                        <span class="shrink-0 w-2 h-2 rounded-full bg-[#9b2247] dark:bg-[#e6d194] mt-1"></span>
                        @endif
                    </div>
                    @if($n->body)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug line-clamp-2">{{ $n->body }}</p>
                    @endif
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 gap-2 text-gray-400 dark:text-gray-500">
                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="text-xs">Sin notificaciones</span>
            </div>
            @endforelse
        </div>

        {{-- Footer admin --}}
        @auth
        @if(auth()->user()->admin())
        <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2 bg-gray-50 dark:bg-gray-900/50">
            <a href="{{ route('admin.notifications') }}" wire:navigate
               class="text-[11px] text-[#9b2247] dark:text-[#e6d194] hover:underline font-semibold transition flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Enviar notificación
            </a>
        </div>
        @endif
        @endauth
    </div>

    {{-- ── LISTENER PUSH ── --}}
    <div
        x-on:new-system-notification.window="
            flash();
            $dispatch('toast', {
                icon: $event.detail.type === 'danger' ? 'error' : ($event.detail.type === 'warning' ? 'warning' : ($event.detail.type === 'success' ? 'success' : 'info')),
                title: $event.detail.title
            });
            $wire.$refresh();
        "
    ></div>

    <style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg); }
        15%       { transform: rotate(-15deg); }
        30%       { transform: rotate(15deg); }
        45%       { transform: rotate(-10deg); }
        60%       { transform: rotate(10deg); }
        75%       { transform: rotate(-5deg); }
        90%       { transform: rotate(5deg); }
    }
    .animate-wiggle { animation: wiggle 0.6s ease-in-out; }
    </style>
</div>
