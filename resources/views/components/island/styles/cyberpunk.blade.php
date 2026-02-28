<div class="flex items-center gap-3 w-full h-full px-2 rounded-full overflow-hidden" x-data="{ flicker: false }" x-init="setInterval(() => flicker = !flicker, 2000)">
    {{-- Decorative Cyberpunk Frame --}}
    <div class="absolute inset-0 border-r-2 border-cyan-400/50 clip-path-cyber"></div>
    
    <div class="shrink-0 flex items-center justify-center bg-cyan-400 text-black px-2 py-0.5 skew-x-[-15deg] shadow-[0_0_10px_rgba(34,211,238,0.8)]">
        <span class="text-[9px] font-black uppercase italic tracking-tighter">System.Link</span>
    </div>

    <div class="flex-1 flex flex-col items-start overflow-hidden">
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-magenta-500 rounded-none transform rotate-45 animate-pulse shadow-[0_0_5px_#f0f]"></span>
            <span x-text="islandMsg" 
                  class="text-[11px] font-black italic uppercase tracking-[0.1em] text-cyan-400 drop-shadow-[0_0_4px_rgba(34,211,238,0.5)]"
                  :class="flicker ? 'opacity-90' : 'opacity-100'"></span>
        </div>
        <div class="h-[1px] w-full bg-gradient-to-r from-cyan-400 via-magenta-500 to-transparent opacity-40"></div>
    </div>

    <div class="shrink-0 flex flex-col text-[7px] font-bold text-magenta-400/70 border-l border-magenta-500/30 pl-2 leading-none">
        <span>LVL.9</span>
        <span>AUTH.OK</span>
    </div>

    <style>
        .clip-path-cyber {
            clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 5% 100%, 0% 80%);
        }
        .bg-magenta-500 { background-color: #ff00ff; }
        .text-magenta-400 { color: #ff00ff; }
        .border-magenta-500 { border-color: #ff00ff; }
    </style>
</div>
