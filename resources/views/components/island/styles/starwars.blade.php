<div x-data="{ 
        jump: true,
        leftStars: Array.from({length: 15}, () => ({ top: Math.random()*100, width: 2+Math.random()*15, dur: 0.3+Math.random()*0.3, del: Math.random()*0.3, yOffset: (Math.random()-0.5)*25 })),
        rightStars: Array.from({length: 15}, () => ({ top: Math.random()*100, width: 2+Math.random()*15, dur: 0.3+Math.random()*0.3, del: Math.random()*0.3, yOffset: (Math.random()-0.5)*25 }))
    }" x-init="
        if(islandMsg) { setTimeout(() => jump = false, 1200); }
        $watch('islandMsg', () => { 
            if(islandMsg) { 
                jump = true; 
                setTimeout(() => jump = false, 1200); 
            } 
        });
    " class="absolute inset-0 overflow-hidden flex items-center justify-center z-10 w-full h-full">

    {{-- Hyperspace Effect (Velocity/Light speed) --}}
    <div x-show="jump" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-150"
        class="absolute inset-0 flex items-center justify-center w-full h-full">

        {{-- Core lightburst / Center Engine --}}
        <div class="absolute w-12 h-12 bg-white rounded-full blur-md opacity-90 animate-pulse"></div>
        <div class="absolute w-40 h-8 rounded-full blur-lg opacity-70"
            :class="islandType === 'error' ? 'bg-red-500' : 'bg-cyan-400'"></div>

        {{-- Left Stars --}}
        <template x-for="(star, i) in leftStars" :key="'l'+i">
            <div class="absolute rounded-full"
                :class="islandType === 'error' ? 'bg-white shadow-[0_0_8px_red]' : 'bg-white shadow-[0_0_8px_cyan]'"
                :style="`top: ${star.top}%; left: 50%; height: ${Math.random() > 0.5 ? 2 : 1}px; width: ${star.width}px; transform-origin: left center; animation: hyperspace-left ${star.dur}s ease-in infinite; animation-delay: ${star.del}s; margin-top: ${star.yOffset}px;`">
            </div>
        </template>

        {{-- Right Stars --}}
        <template x-for="(star, i) in rightStars" :key="'r'+i">
            <div class="absolute rounded-full"
                :class="islandType === 'error' ? 'bg-white shadow-[0_0_8px_red]' : 'bg-white shadow-[0_0_8px_cyan]'"
                :style="`top: ${star.top}%; right: 50%; height: ${Math.random() > 0.5 ? 2 : 1}px; width: ${star.width}px; transform-origin: right center; animation: hyperspace-right ${star.dur}s ease-in infinite; animation-delay: ${star.del}s; margin-top: ${star.yOffset}px;`">
            </div>
        </template>
    </div>

    {{-- Final Result (Notification) --}}
    <div x-show="!jump" x-transition:enter="transition transform ease-out duration-700 delay-[300ms]"
        x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
        class="relative z-20 flex items-center justify-center gap-3 w-full px-6">

        <div class="flex items-center justify-center shrink-0">
            <template x-if="islandType === 'success'">
                <i
                    class="fas fa-check-circle text-[#FFE81F] text-xl drop-shadow-[0_0_8px_rgba(255,232,31,0.6)] animate-pulse"></i>
            </template>
            <template x-if="islandType === 'error'">
                <i
                    class="fas fa-exclamation-triangle text-red-500 text-xl drop-shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse"></i>
            </template>
            <template x-if="islandType !== 'success' && islandType !== 'error'">
                <i class="fas fa-jedi text-cyan-400 text-xl drop-shadow-[0_0_8px_rgba(34,211,238,0.6)]"></i>
            </template>
        </div>

        <div class="overflow-hidden relative flex flex-1 items-center min-w-0">
            <div :class="islandMsg && islandMsg.length > 25 ? 'animate-marquee whitespace-nowrap' : ''"
                :style="islandMsg && islandMsg.length > 25 ? `animation-duration: ${Math.max(4, islandMsg.length * 0.12)}s` : ''"
                class="inline-block relative">
                <span x-text="islandMsg" class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest"
                    :class="islandType === 'error' ? 'text-red-500 drop-shadow-[0_0_5px_rgba(239,68,68,0.8)]' : ''"
                    :style="islandType !== 'error' ? 'color: #FFE81F; text-shadow: 0 0 8px rgba(255, 232, 31, 0.5), 0 2px 4px rgba(0,0,0,0.8);' : ''"></span>
            </div>
        </div>
    </div>

    <style>
        @keyframes hyperspace-right {
            0% {
                transform: translateX(5px) scaleX(1);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            100% {
                transform: translateX(200px) scaleX(5);
                opacity: 0;
            }
        }

        @keyframes hyperspace-left {
            0% {
                transform: translateX(-5px) scaleX(1);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            100% {
                transform: translateX(-200px) scaleX(5);
                opacity: 0;
            }
        }
    </style>
</div>