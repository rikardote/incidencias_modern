<div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-visible">
    
    {{-- Main Core Orb (Centered within the oval) --}}
    <div class="relative w-10 h-10 flex items-center justify-center rounded-full bg-white/5 shadow-[0_0_15px_rgba(255,255,255,0.15)]">
        {{-- Spinning Rings --}}
        <div class="absolute inset-0 border-2 rounded-full border-t-white animate-[spin_0.8s_linear_infinite]"
             :class="islandType === 'error' ? 'border-t-red-500' : 'border-t-emerald-400'"></div>
        <div class="absolute inset-1.5 border-[1px] rounded-full border-white/20 animate-[spin_1.5s_linear_infinite_reverse]"></div>
        
        {{-- High Impact Icon --}}
        <div class="relative z-10">
            <template x-if="islandType === 'success'">
                <i class="fas fa-check text-emerald-400 text-xl animate-bounce"></i>
            </template>
            <template x-if="islandType === 'error'">
                <i class="fas fa-times text-red-500 text-xl animate-pulse"></i>
            </template>
            <template x-if="islandType !== 'success' && islandType !== 'error'">
                <i class="fas fa-bolt text-blue-400 text-xl animate-pulse"></i>
            </template>
        </div>

        {{-- Energy Ring --}}
        <div class="absolute inset-0 rounded-full border-2 border-white/30 animate-ping"></div>
    </div>

    {{-- Kinetic Energy Lines (Left and Right of the core) --}}
    <div class="absolute inset-0 flex items-center justify-between px-8">
        {{-- Left Streak --}}
        <div class="h-1 w-24 rounded-full bg-gradient-to-r from-transparent animate-[kinetic-streak-left_1s_infinite]"
             :class="islandType === 'error' ? 'to-red-500/50' : 'to-emerald-400/50'"></div>
        
        {{-- Right Streak --}}
        <div class="h-1 w-24 rounded-full bg-gradient-to-l from-transparent animate-[kinetic-streak-right_1s_infinite]"
             :class="islandType === 'error' ? 'to-red-500/50' : 'to-emerald-400/50'"></div>
    </div>

    {{-- Kinetic Particles (Expanding within the oval) --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <template x-for="i in [1,2,3,4,5,6,7,8]" :key="i">
            <div class="absolute" :style="`transform: rotate(${i * 45}deg)`">
                <div class="w-1.5 h-1.5 rounded-full animate-[kinetic-pop_1s_ease-out_infinite]"
                     :class="islandType === 'error' ? 'bg-red-500 shadow-[0_0_8px_red]' : 'bg-emerald-400 shadow-[0_0_8px_#4ade80]'"
                     :style="`animation-delay: ${i * 0.1}s`">
                </div>
            </div>
        </template>
    </div>

    <style>
        @keyframes kinetic-pop {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-30px) scale(0); opacity: 0; }
        }
        @keyframes kinetic-streak-left {
            0% { transform: translateX(50px) scaleX(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(-100px) scaleX(1.5); opacity: 0; }
        }
        @keyframes kinetic-streak-right {
            0% { transform: translateX(-50px) scaleX(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100px) scaleX(1.5); opacity: 0; }
        }
    </style>
</div>
