{{-- resources/views/components/island/styles/matrix.blade.php --}}
<div class="flex items-center gap-3 w-full h-full px-2 text-white overflow-hidden relative">
    <!-- Background: Matrix Rain -->
    <div class="absolute inset-y-0 left-0 flex gap-1 opacity-20 pointer-events-none select-none">
        <div class="flex flex-col text-[5px] text-green-500 animate-matrix-fall-slow h-full">
            <span>0</span><span>1</span><span>0</span><span>1</span><span>0</span>
        </div>
        <div class="flex flex-col text-[5px] text-green-400 animate-matrix-fall-fast h-full">
            <span>1</span><span>0</span><span>1</span><span>0</span><span>1</span>
        </div>
    </div>

    <!-- Core: System Hub -->
    <div class="shrink-0 z-10 flex items-center justify-center">
        <div
            class="relative h-8 w-8 flex items-center justify-center border border-green-400/20 bg-black/60 shadow-[0_0_12px_rgba(34,211,94,0.15)] overflow-hidden rounded-sm">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#4ade80 0.5px, transparent 0); background-size: 3px 3px;">
            </div>
            <div class="absolute inset-1 border border-dashed border-green-500/30 rounded-full animate-spin-slow"></div>
            <div class="relative z-10"
                :class="{ 'text-red-500': islandType === 'error', 'text-green-400': islandType !== 'error' }">
                <template x-if="islandType === 'success'"><i
                        class="fas fa-shield-alt text-[10px] animate-pulse"></i></template>
                <template x-if="islandType === 'error'"><i
                        class="fas fa-skull-crossbones text-[10px] animate-pulse"></i></template>
                <template x-if="islandType === 'warning'"><i
                        class="fas fa-radiation text-oro text-[10px] animate-pulse"></i></template>
                <template x-if="islandType === 'info' || !islandType"><i
                        class="fas fa-satellite-dish text-[10px] animate-pulse"></i></template>
            </div>
            <div class="absolute inset-x-0 h-[1.5px] bg-green-400/60 shadow-[0_0_8px_#4ade80] animate-scan-line"></div>
        </div>
    </div>

    <!-- Message area: Clean Marquee -->
    <div class="flex-1 min-w-0 z-10 flex items-center overflow-hidden h-full">
        <div class="flex items-center" :class="(islandMsg || '').length > 15 ? 'matrix-marquee-active' : ''">
            <span x-text="islandMsg"
                class="text-[11px] font-black text-green-400 uppercase tracking-wider nothing-font drop-shadow-[0_0_5px_rgba(74,222,128,0.4)] whitespace-nowrap px-2">
            </span>
        </div>
    </div>

    <!-- Right Decoration -->
    <div class="shrink-0 flex items-center gap-1 opacity-40 z-10">
        <div class="w-1 h-3 bg-green-500/20 animate-pulse"></div>
        <div class="w-1 h-2 bg-green-500/60"></div>
    </div>

    <style>
        @keyframes matrix-fall-anim {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        .animate-matrix-fall-slow {
            animation: matrix-fall-anim 5s linear infinite;
        }

        .animate-matrix-fall-fast {
            animation: matrix-fall-anim 3s linear infinite;
        }

        @keyframes matrix-spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: matrix-spin-slow 12s linear infinite;
        }

        @keyframes matrix-scan-line {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(100%);
            }
        }

        .animate-scan-line {
            animation: matrix-scan-line 2s linear infinite;
        }

        @keyframes matrix-marquee-reveal {
            0% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(0);
            }

            80% {
                transform: translateX(calc(-100% + 80px));
            }

            100% {
                transform: translateX(calc(-100% + 80px));
            }
        }

        .matrix-marquee-active {
            animation: matrix-marquee-reveal 5s linear infinite;
        }
    </style>
</div>