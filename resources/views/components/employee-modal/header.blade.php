@props([
'editingEmployeeId' => null,
'name' => '',
'father_lastname' => '',
'num_empleado' => '',
'curp' => '',
'gender' => ''
])

<div class="relative px-8 py-10 bg-gradient-to-br from-[#13322B] via-[#1a4038] to-[#9b2247] text-white overflow-hidden">
    {{-- Elementos decorativos --}}
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/5 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-[#e6d194]/10 rounded-full blur-2xl opacity-30">
    </div>

    <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
        {{-- Avatar Grande con Iniciales --}}
        <div
            class="shrink-0 w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center shadow-2xl transform hover:rotate-3 transition duration-500">
            @if($editingEmployeeId)
            <span class="text-3xl font-black text-[#e6d194] uppercase tracking-tighter">
                {{ strtoupper(mb_substr($name, 0, 1)) }}{{ strtoupper(mb_substr($father_lastname, 0, 1)) }}
            </span>
            @else
            <svg class="w-12 h-12 text-[#e6d194]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            @endif
        </div>

        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-baseline gap-2 mb-1">
                <h3 class="text-2xl font-black tracking-tight text-white uppercase leading-none">
                    {{ $editingEmployeeId ? ($name . ' ' . $father_lastname) : 'Nuevo Empleado' }}
                </h3>

                @if($editingEmployeeId || $curp)
                <span
                    class="px-2 py-1 rounded-lg text-[9px] font-black tracking-widest uppercase {{ $gender === 'Masculino' ? 'bg-blue-500/20 text-blue-200 border border-blue-500/30' : ($gender === 'Femenino' ? 'bg-pink-500/20 text-pink-200 border border-pink-500/30' : 'bg-white/10 text-white/50 border border-white/10') }}">
                    {{ $gender }}
                </span>
                @endif

                @if($editingEmployeeId)
                <span
                    class="bg-[#e6d194] text-[#13322B] px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase">
                    #{{ $num_empleado }}
                </span>
                @endif
            </div>
            <p class="text-[#e6d194]/80 text-xs font-medium uppercase tracking-[0.2em]">
                {{ $editingEmployeeId ? 'Mantenimiento de Ficha de Personal' : 'Registro de nuevo ingreso al sistema' }}
            </p>
        </div>

        <button type="button" wire:click="$set('showEmployeeModal', false)"
            class="absolute top-0 right-0 p-2 text-white/40 hover:text-white hover:bg-white/10 rounded-full transition-all duration-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>