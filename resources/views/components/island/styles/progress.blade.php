<div class="w-full flex items-center justify-between gap-4">
    <div class="shrink-0 flex items-center gap-2">
        <i class="fas fa-sync-alt text-[10px] animate-spin" :class="(islandMsg || '').includes('Eliminada') ? 'text-amber-400' : 'text-green-400'"></i>
        <span class="text-[10px] font-black nothing-font uppercase tracking-tighter" 
              :class="(islandMsg || '').includes('Eliminada') ? 'text-amber-400' : 'text-green-400'"
              x-text="(islandMsg || '').includes('Eliminada') ? 'Eliminando' : ((islandMsg || '').includes('Generando') ? 'Generando' : 'Capturando')"></span>
    </div>
    <div class="flex-1 h-1.5 rounded-full overflow-hidden border"
         :class="(islandMsg || '').includes('Eliminada') ? 'bg-amber-500/10 border-amber-500/20' : 'bg-green-500/10 border-green-500/20'">
        <div class="h-full shadow-[0_0_8px] transition-all duration-75" 
             :class="(islandMsg || '').includes('Eliminada') ? 'bg-amber-500 shadow-amber-500/50' : 'bg-green-400 shadow-green-400/50'"
             :style="'width: ' + progress + '%'"></div>
    </div>
    <span class="text-[11px] font-black text-white nothing-font w-8 text-right" x-text="progress + '%'"></span>
</div>
