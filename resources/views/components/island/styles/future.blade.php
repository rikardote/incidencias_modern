{{-- resources/views/components/island/styles/future.blade.php --}}
<div class="flex items-center gap-3 w-full min-w-0 px-4 text-white overflow-hidden">
    <!-- Icono con efecto Radar -->
    <div class="shrink-0 flex items-center justify-center w-6 relative">
        <template x-if="progress === 0">
            <div class="absolute inset-0 bg-green-400/20 rounded-full animate-ping"></div>
        </template>
        <i class="fas fa-terminal text-green-400 text-xs relative" :class="progress > 0 ? 'animate-pulse' : ''"></i>
    </div>

    <!-- Marca Dinámica -->
    <div
        class="nothing-font uppercase font-black tracking-widest text-[9px] text-green-400 whitespace-nowrap opacity-80">
        <span x-text="progress > 0 ? '[ RUNNING ]' : '[ READY ]'" class="animate-pulse"></span>
    </div>

    <!-- Mensaje con Resplandor -->
    <div class="flex-1 min-w-0 nothing-font uppercase font-black tracking-widest text-[11px] truncate text-green-50"
        style="text-shadow: 0 0 8px rgba(74, 222, 128, 0.6);" x-text="islandMsg || 'ESPERANDO COMANDO...'">
    </div>

    <!-- Barra de Carga "Hacker" -->
    <div class="flex items-center gap-3 w-28 shrink-0">
        <div class="flex-1 h-1 bg-green-900/40 rounded-full overflow-hidden border border-green-500/20 relative">
            <!-- Efecto de escaneo cuando está en 0 -->
            <template x-if="progress <= 0">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-green-400/40 to-transparent w-1/2 animate-marquee">
                </div>
            </template>
            <!-- Barra de progreso real -->
            <div class="h-full bg-green-400 transition-all duration-500 ease-out shadow-[0_0_15px_#4ade80]"
                :style="'width: ' + progress + '%' "></div>
        </div>
        <div class="nothing-font text-[10px] font-black text-green-400 min-w-[30px] text-right tabular-nums"
            x-text="progress + '%' ">
        </div>
    </div>
</div>