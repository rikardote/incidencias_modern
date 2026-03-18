<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Notificaciones del Sistema</h1>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-widest">Gestión de comunicados internos y alertas</p>
        </div>
        <button wire:click="openModal"
            class="flex items-center justify-center gap-2 bg-[#9b2247] hover:bg-[#611232] text-white font-black text-[11px] uppercase tracking-[0.15em] px-6 py-3 rounded-xl shadow-lg shadow-red-900/20 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Notificación
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/50 rounded-2xl shadow-xl overflow-hidden active:shadow-oro/5 transition-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 uppercase text-[10px] font-black tracking-[0.2em] border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left">Mensaje</th>
                        <th class="px-6 py-4 text-center">Tipo</th>
                        <th class="px-6 py-4 text-left">Destinatario</th>
                        <th class="px-6 py-4 text-left">Autor</th>
                        <th class="px-6 py-4 text-left">Enviado</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->notifications as $n)
                    @php
                        $badge = [
                            'info'    => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                            'success' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400 border-green-200 dark:border-green-500/20',
                            'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-400/10 dark:text-yellow-400 border-yellow-200 dark:border-yellow-500/20',
                            'danger'  => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400 border-red-200 dark:border-red-500/20',
                        ][$n->type] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                    @endphp
                    <tr wire:key="row-{{ $n->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 dark:text-gray-100">{{ $n->title }}</div>
                            @if($n->body)
                            <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-1 italic">{{ $n->body }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $typeLabels = [
                                    'info'    => 'Informativo',
                                    'success' => 'Éxito',
                                    'warning' => 'Advertencia',
                                    'danger'  => 'Urgente',
                                ];
                            @endphp
                            <span class="text-[9px] font-black px-2.5 py-1 rounded-full border {{ $badge }}">
                                {{ $typeLabels[$n->type] ?? $n->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                    {{ $n->targetUser ? $n->targetUser->name : '🌐 Broadcast Global' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tighter">{{ $n->sender->name ?? 'SISTEMA' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tight">{{ $n->created_at->format('d/m/Y') }}</div>
                            <div class="text-[9px] text-gray-400 dark:text-gray-600">{{ $n->created_at->format('H:i') }} hrs</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($deleteId === $n->id)
                            <div class="flex items-center gap-2 justify-end">
                                <span class="text-[10px] font-black text-red-500 uppercase">¿Borrar?</span>
                                <button wire:click="destroy" class="p-1 px-2.5 bg-red-500 text-white rounded-lg text-[10px] font-black">SÍ</button>
                                <button wire:click="$set('deleteId', null)" class="p-1 px-2.5 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-[10px] font-black">NO</button>
                            </div>
                            @else
                            <button wire:click="confirmDelete({{ $n->id }})"
                                class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition p-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">No hay historial de notificaciones</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->notifications->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
            {{ $this->notifications->links() }}
        </div>
        @endif
    </div>

    {{-- ── MODAL NUEVA NOTIFICACIÓN ── --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeModal()">
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 dark:border-gray-700">
                <div class="px-8 pt-8 pb-6 bg-[#9b2247] dark:bg-gray-950 relative overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/10">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white uppercase tracking-tight">Crear Aviso</h3>
                                <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Notificación de sistema</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="p-2 text-white/50 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-6">
                    {{-- Tipo --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">Importancia</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([
                                'info'    => ['bg' => 'peer-checked:border-blue-500 peer-checked:text-blue-500', 'icon' => '🔵', 'label' => 'Informativo'],
                                'success' => ['bg' => 'peer-checked:border-emerald-500 peer-checked:text-emerald-500', 'icon' => '🟢', 'label' => 'Éxito'],
                                'warning' => ['bg' => 'peer-checked:border-yellow-500 peer-checked:text-yellow-500', 'icon' => '🟡', 'label' => 'Advertencia'],
                                'danger'  => ['bg' => 'peer-checked:border-red-500 peer-checked:text-red-500', 'icon' => '🔴', 'label' => 'Urgente']
                            ] as $val => $data)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="type" value="{{ $val }}" class="sr-only peer">
                                <div class="px-2 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-transparent rounded-2xl text-center transition-all duration-200 peer-checked:shadow-xl peer-checked:shadow-current/5 {{ $data['bg'] }} hover:border-gray-200 dark:hover:border-gray-700">
                                    <div class="text-sm mb-1 leading-none">{{ $data['icon'] }}</div>
                                    <div class="text-[9px] font-black uppercase tracking-widest opacity-60 peer-checked:opacity-100">{{ $data['label'] }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Destinatario --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">Audiencia</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="target" value="all" class="sr-only peer">
                                <div class="py-3 bg-gray-50 dark:bg-gray-900 border-2 border-transparent rounded-2xl text-center transition-all peer-checked:border-oro peer-checked:text-oro peer-checked:shadow-lg peer-checked:shadow-oro/10 hover:border-gray-200 dark:hover:border-gray-700">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 peer-checked:text-oro">Publicar a Todos</div>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="target" value="user" class="sr-only peer">
                                <div class="py-3 bg-gray-50 dark:bg-gray-900 border-2 border-transparent rounded-2xl text-center transition-all peer-checked:border-oro peer-checked:text-oro peer-checked:shadow-lg peer-checked:shadow-oro/10 hover:border-gray-200 dark:hover:border-gray-700">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 peer-checked:text-oro">Usuario Único</div>
                                </div>
                            </label>
                        </div>
                        @if($target === 'user')
                        <div class="mt-4 animate-fadeIn">
                            <select wire:model="targetUserId"
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-2xl px-4 py-3 focus:ring-2 focus:ring-oro/20 focus:border-oro focus:outline-none transition-all">
                                <option value="">— Seleccionar Destinatario —</option>
                                @foreach($this->users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->type }})</option>
                                @endforeach
                            </select>
                            @error('targetUserId') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Título --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">Encabezado de la Notificación</label>
                        <input wire:model="title" type="text" maxlength="120"
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-2xl px-4 py-3 focus:ring-2 focus:ring-oro/20 focus:border-oro focus:outline-none transition-all placeholder:text-gray-400 placeholder:italic"
                            placeholder="Ej: Mantenimiento programado para las 15:00 hrs">
                        @error('title') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                    </div>

                    {{-- Cuerpo --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">Contenido Detallado (opcional)</label>
                        <textarea wire:model="body" rows="3" maxlength="1000"
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-2xl px-4 py-3 focus:ring-2 focus:ring-oro/20 focus:border-oro focus:outline-none transition-all resize-none placeholder:text-gray-400 placeholder:italic"
                            placeholder="Proporcione más información aquí..."></textarea>
                        @error('body') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="send" wire:loading.attr="disabled"
                        class="flex-1 py-3.5 bg-[#9b2247] hover:bg-[#611232] text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-red-900/20 transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="send">Publicar Ahora 🚀</span>
                        <span wire:loading wire:target="send" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enviando...
                        </span>
                    </button>
                    <button wire:click="closeModal"
                        class="flex-1 py-3.5 bg-transparent border-2 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-colors hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>
