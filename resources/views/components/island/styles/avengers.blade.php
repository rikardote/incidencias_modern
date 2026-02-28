<div x-data="{ 
        message: islandMsg,
        letters: [],
        disintegrating: false,
        init() {
            this.prepareLetters();
            this.$watch('islandMsg', () => {
                if (islandMsg) {
                    this.message = islandMsg;
                    this.prepareLetters();
                    this.disintegrating = false;
                    setTimeout(() => {
                        this.disintegrating = true;
                    }, 1200);
                }
            });
            if (this.message) {
                 setTimeout(() => {
                     this.disintegrating = true;
                 }, 1200);
            }
        },
        prepareLetters() {
            if (this.message) {
                this.letters = String(this.message).split('').map((char, i) => {
                    const isSpace = char === ' ';
                    return {
                        char: isSpace ? '&nbsp;' : char,
                        id: i,
                        delay: (i * 0.03) + (Math.random() * 0.1), 
                        x: (Math.random() - 0.5) * 80 + 30, // Más empuje a la derecha
                        y: (Math.random() - 0.5) * -60 - 20, // Empuje hacia arriba
                        rotate: (Math.random() - 0.5) * 180
                    };
                });
            }
        }
    }" 
    class="absolute inset-0 flex items-center justify-center overflow-hidden z-10 w-full h-full rounded-full clip-path-inset">

    <div class="relative z-20 flex items-center justify-center gap-3 w-full px-6">
         <!-- Icono Cinemático -->
         <div class="flex items-center justify-center shrink-0"
              :class="disintegrating ? 'transition-all duration-1000 opacity-0 scale-50 delay-500 blur-sm' : 'opacity-100 scale-100'">
             <template x-if="islandType === 'success' && !(islandMsg || '').toLowerCase().includes('eliminad')">
                 <svg class="w-6 h-6 text-emerald-500 drop-shadow-[0_0_8px_rgba(16,185,129,0.8)] fill-current" viewBox="0 0 24 24">
                     <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                 </svg> <!-- Palomita OK -->
             </template>
             <template x-if="islandType === 'error' || islandType === 'warning' || (islandType === 'success' && (islandMsg || '').toLowerCase().includes('eliminad'))">
                 <svg class="w-6 h-6 text-red-500 drop-shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse fill-current" viewBox="0 0 24 24">
                     <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                 </svg> <!-- Bote de Basura -->
             </template>
             <template x-if="!(islandType === 'success' && !(islandMsg || '').toLowerCase().includes('eliminad')) && !(islandType === 'error' || islandType === 'warning' || (islandType === 'success' && (islandMsg || '').toLowerCase().includes('eliminad')))">
                 <svg class="w-6 h-6 text-blue-500 drop-shadow-[0_0_8px_rgba(59,130,246,0.8)] fill-current" viewBox="0 0 24 24">
                     <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                 </svg> <!-- Campana de Notificación -->
             </template>
         </div>

         <!-- Contenedor del Texto Cinematográfico -->
         <div class="relative flex flex-1 items-center justify-center font-black uppercase tracking-widest text-center"
              style="font-family: 'Arial Black', Impact, sans-serif; letter-spacing: 0.15em;">
             
             <!-- Texto con Letras Independientes (The Snap) -->
             <div class="flex items-center justify-center text-[10px] sm:text-[11px] whitespace-nowrap"
                  :class="islandType === 'error' ? 'text-red-500 text-shadow-red' : 'text-gray-200 text-shadow-white'">
                 <template x-for="l in letters" :key="l.id">
                     <span x-html="l.char"
                           class="inline-block will-change-transform"
                           :style="disintegrating 
                                ? `transform: translate(${l.x}px, ${l.y}px) rotate(${l.rotate}deg) scale(0); opacity: 0; filter: blur(5px); transition: all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94); transition-delay: ${l.delay}s;` 
                                : 'transform: none; opacity: 1; filter: blur(0); transition: none;'">
                     </span>
                 </template>
             </div>
         </div>
    </div>
    
    <style>
        .text-shadow-white { text-shadow: 0 2px 4px rgba(0,0,0,0.8), 0 0 5px rgba(255,255,255,0.4); }
        .text-shadow-red { text-shadow: 0 2px 4px rgba(0,0,0,0.8), 0 0 5px rgba(239,68,68,0.6); }
    </style>
</div>
