<div class="w-full flex items-center justify-between gap-3 overflow-hidden">
    <div class="shrink-0 flex items-center gap-2">
        <i class="fas fa-sync-alt text-[10px] animate-spin"
            :class="(islandMsg || '').includes('Eliminada') ? 'text-amber-400' : 'text-green-400'"></i>
    </div>
    <div class="flex-1 min-w-0 overflow-hidden relative">
        <div :class="(islandMsg || '').length > 20 ? 'animate-marquee whitespace-nowrap' : ''"
            :style="(islandMsg || '').length > 20 ? `animation-duration: ${Math.max(3, (islandMsg || '').length * 0.1)}s` : ''"
            class="inline-block relative">
            <span class="text-[9px] font-black nothing-font uppercase tracking-tighter"
                :class="(islandMsg || '').includes('Eliminada') ? 'text-amber-400' : 'text-green-400'" x-text="(islandMsg || '').includes('Eliminada') ? 'Eliminando' : 
                         ((islandMsg || '').includes('Generando') ? 'Generando' : 
                         ((islandMsg || '').includes('Capturada') ? 'Capturando' : (islandMsg || '')))"></span>
        </div>
    </div>
    <div class="flex-1 h-1.5 rounded-full overflow-hidden border border-white/10 bg-white/5">
        <div class="h-full shadow-[0_0_8px] transition-all duration-75"
            :class="(islandMsg || '').includes('Eliminada') ? 'bg-amber-500 shadow-amber-500/50' : 'bg-green-400 shadow-green-400/50'"
            :style="'width: ' + progress + '%'"></div>
    </div>
    <span class="text-[10px] font-black text-white/50 nothing-font shrink-0" x-text="Math.floor(progress) + '%'"></span>
</div>