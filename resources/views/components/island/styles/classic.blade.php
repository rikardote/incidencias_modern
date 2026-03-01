<div class="flex items-center gap-3 max-w-full">
    <div class="shrink-0 flex items-center">
        <template x-if="islandType === 'success'"><i class="fas fa-check-circle text-green-400 text-sm"></i></template>
        <template x-if="islandType === 'error'"><i class="fas fa-times-circle text-red-500 text-sm"></i></template>
        <template x-if="islandType === 'warning'"><i
                class="fas fa-exclamation-triangle text-amber-400 text-sm"></i></template>
    </div>
    <div class="overflow-hidden relative flex items-center min-w-0 flex-1">
        <div :class="islandMsg && islandMsg.length > 25 ? 'animate-marquee whitespace-nowrap' : ''"
            :style="islandMsg && islandMsg.length > 25 ? `animation-duration: ${Math.max(4, islandMsg.length * 0.12)}s` : ''"
            class="inline-block">
            <span x-text="islandMsg"
                class="text-[13px] font-black text-white uppercase tracking-wider nothing-font"></span>
        </div>
    </div>
</div>