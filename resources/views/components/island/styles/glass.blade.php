<div class="flex items-center gap-4 bg-white/5 backdrop-blur-md px-4 py-1 rounded-full border border-white/10 shadow-xl">
    <div class="w-2 h-2 rounded-full animate-pulse" :class="islandType === 'success' ? 'bg-green-400' : (islandType === 'error' ? 'bg-red-500' : 'bg-amber-400')"></div>
    <span x-text="islandMsg" class="text-[11px] font-black text-white uppercase tracking-widest nothing-font"></span>
</div>
