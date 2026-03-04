@props(['employee'])

<div wire:key="emp-{{ $employee->id }}"
    class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md hover:border-[#13322B]/30 dark:hover:border-[#e6d194]/20 transition-all duration-200 overflow-hidden">

    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 px-4 py-3">
        <div class="flex items-center gap-4 min-w-0 w-full xl:w-auto">
            {{-- Avatar --}}
            <div
                class="shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-[#13322B]/10 to-[#9b2247]/10 dark:from-[#13322B]/30 dark:to-[#9b2247]/30 border border-[#13322B]/5 dark:border-[#e6d194]/10 flex items-center justify-center shadow-sm">
                <span class="text-xs font-black text-[#13322B] dark:text-[#e6d194] leading-none tracking-tighter">
                    {{ strtoupper(mb_substr($employee->name, 0, 1)) }}{{
                    strtoupper(mb_substr($employee->father_lastname, 0, 1)) }}
                </span>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs font-bold text-[#9b2247] dark:text-[#e6d194] shrink-0">
                        {{ $employee->num_empleado }}
                    </span>
                    <span class="text-gray-200 dark:text-gray-600 text-xs">|</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
                        {{ $employee->fullname }}
                    </span>
                    <span
                        class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter {{ $employee->gender === 'Masculino' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                        {{ $employee->gender }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $employee->department->description ?? 'Sin depto.' }}
                    </span>
                    @if($employee->puesto)
                    <span class="text-gray-200 dark:text-gray-600 text-xs">·</span>
                    <span class="text-[11px] text-[#13322B]/60 dark:text-[#e6d194]/60">
                        {{ $employee->puesto->puesto }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div
            class="shrink-0 flex flex-wrap items-center justify-around sm:justify-end gap-1 w-full xl:w-auto border-t xl:border-t-0 border-gray-100 dark:border-gray-700 pt-3 xl:pt-0">
            @php
            $isMaintenance = \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
            !auth()->user()->admin();
            @endphp

            {{-- Incidencias --}}
            @if($isMaintenance)
            <button type="button" onclick="window.Swal.fire({ 
                    icon: 'error', 
                    title: 'Mantenimiento Activo', 
                    text: 'La captura de incidencias está suspendida temporalmente por administración.',
                    confirmButtonColor: '#9b2247'
                })"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed opacity-50 grayscale transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Mantenimiento</span>
            </button>
            @elseif(!$employee->active)
            <button type="button" onclick="window.Swal.fire({ 
                    icon: 'warning', 
                    title: 'Personal Inactivo', 
                    text: 'No se pueden capturar incidencias para empleados con estatus de Baja.',
                    confirmButtonColor: '#9b2247'
                })"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed opacity-60 grayscale transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Incidencias</span>
            </button>
            @else
            <a href="{{ route('employees.incidencias', $employee->id) }}" wire:navigate
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-[#9b2247] dark:text-[#e6d194] hover:bg-[#9b2247]/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Incidencias</span>
            </a>
            @endif

            <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

            {{-- Biométrico --}}
            @if(!$employee->active)
            <button type="button" onclick="window.Swal.fire({ 
                    icon: 'warning', 
                    title: 'Ficha Desactivada', 
                    text: 'El acceso al historial biométrico está restringido para personal inactivo.',
                })"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed opacity-60 grayscale transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Biométrico</span>
            </button>
            @else
            <a href="{{ route('employees.biometrico', $employee->id) }}" wire:navigate
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-400 hover:text-[#13322B] dark:hover:text-[#e6d194] hover:bg-[#13322B]/5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Biométrico</span>
            </a>
            @endif

            <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

            {{-- Vacaciones --}}
            @if(!$employee->active)
            <button type="button" onclick="window.Swal.fire({ 
                    icon: 'warning', 
                    title: 'Personal Inactivo', 
                    text: 'No se pueden consultar vacaciones de personal con estatus de Baja.',
                })"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed opacity-60 grayscale transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Vacaciones</span>
            </button>
            @else
            <a href="{{ route('employees.vacaciones', $employee->id) }}" wire:navigate
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-oro hover:bg-oro/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Vacaciones</span>
            </a>
            @endif

            <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

            {{-- Kardex --}}
            <a href="{{ route('employees.kardex', $employee->id) }}" wire:navigate
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-[#13322B] dark:text-[#e6d194] hover:bg-[#13322B]/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Kardex</span>
            </a>

            <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

            {{-- Estadística --}}
            <a href="{{ route('employees.estadisticas', $employee->id) }}" wire:navigate
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Estadística</span>
            </a>

            <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

            {{-- Información (Editar) --}}
            <button wire:click="edit({{ $employee->id }})"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400">Información</span>
            </button>
        </div>
    </div>
</div>