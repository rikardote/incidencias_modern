<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IslandWidgetService
{
    const CACHE_KEY = 'island_style';
    const DEFAULT_STYLE = 'classic';

    /**
     * Obtiene el estilo actual de la Isla Dinámica.
     */
    public function getCurrentStyle(): string
    {
        return Cache::get(self::CACHE_KEY, self::DEFAULT_STYLE);
    }

    /**
     * Actualiza el estilo global y retorna el nuevo valor.
     */
    public function setStyle(string $style): string
    {
        $validStyles = ['classic', 'progress', 'minimal', 'glass', 'cyberpunk', 'matrix', 'kinetic', 'starwars', 'avengers'];

        if (!in_array($style, $validStyles)) {
            $style = self::DEFAULT_STYLE;
        }

        Cache::forever(self::CACHE_KEY, $style);
        return $style;
    }

    /**
     * Retorna la lista de estilos disponibles con sus descripciones.
     */
    public function getAvailableStyles(): array
    {
        return [
            'classic' => 'Gesto ASCII + Texto. El balance perfecto.',
            'progress' => 'Barra de proceso técnica. Ideal para capturas.',
            'minimal' => 'Solo texto. Rápido y limpio.',
            'glass' => 'Icono + Blur. Estética refinada y moderna.',
            'cyberpunk' => 'Neón + Glitch. Estética futurista y agresiva.',
            'matrix' => 'Lluvia de código + Terminal. El origen.',
            'kinetic' => 'Energía Cinética. 100% Visual, sin textos.',
            'starwars' => 'Hyperspace. Velocidad luz hasta tus notificaciones.',
            'avengers' => 'The Snap. Desintegración cinematográfica al terminar.'
        ];
    }
}