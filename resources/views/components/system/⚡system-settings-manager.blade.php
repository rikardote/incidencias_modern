<?php

use Livewire\Component;
use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;

new class extends Component
{
    public bool $isMaintenanceMode = false;
    public bool $isGeoBlockEnabled = false;
    public bool $isChatAllToAll = false;
    public bool $isTxtLimitsUnlocked = false;

    public function mount()
    {
        if (!auth()->user()->admin()) {
            abort(403);
        }

        $this->isMaintenanceMode = Cache::get('capture_maintenance', false);
        $this->isGeoBlockEnabled = Configuration::get('geo_block_mexico', false);
        $this->isChatAllToAll = Configuration::get('chat_all_to_all', false);
        $this->isTxtLimitsUnlocked = Configuration::get('unlock_txt_limits', false);
    }

    public function toggleMaintenance()
    {
        $this->isMaintenanceMode = !$this->isMaintenanceMode;
        if ($this->isMaintenanceMode) {
            Cache::forever('capture_maintenance', true);
        } else {
            Cache::forget('capture_maintenance');
        }

        broadcast(new \App\Events\GlobalMaintenanceEvent($this->isMaintenanceMode));

        $this->dispatch('maintenance-updated', mode: $this->isMaintenanceMode);

        $this->dispatch('toast', [
            'icon' => $this->isMaintenanceMode ? 'error' : 'success',
            'title' => $this->isMaintenanceMode ? 'Mantenimiento Activado' : 'Mantenimiento Desactivado'
        ]);
    }

    public function toggleGeoBlock()
    {
        $this->isGeoBlockEnabled = !$this->isGeoBlockEnabled;
        Configuration::set('geo_block_mexico', $this->isGeoBlockEnabled);

        $this->dispatch('toast', [
            'icon' => $this->isGeoBlockEnabled ? 'success' : 'info',
            'title' => $this->isGeoBlockEnabled ? 'Geo-protección Activada' : 'Geo-protección Desactivada',
        ]);
    }

    public function toggleChat()
    {
        $this->isChatAllToAll = !$this->isChatAllToAll;
        Configuration::set('chat_all_to_all', $this->isChatAllToAll);

        $this->dispatch('toast', [
            'icon' => $this->isChatAllToAll ? 'success' : 'info',
            'title' => $this->isChatAllToAll ? 'Chat TODOS-TODOS Activado' : 'Chat restringido a administradores',
        ]);
    }

    public function toggleTxtLimits()
    {
        $this->isTxtLimitsUnlocked = !$this->isTxtLimitsUnlocked;
        Configuration::set('unlock_txt_limits', $this->isTxtLimitsUnlocked);

        $this->dispatch('toast', [
            'icon' => $this->isTxtLimitsUnlocked ? 'success' : 'info',
            'title' => $this->isTxtLimitsUnlocked ? 'Límites TxT Desbloqueados' : 'Límites TxT Restablecidos',
        ]);
    }
};
?>

<div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 h-full">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            </svg>
        </div>
        <div>
            <h5 class="text-xl font-black text-gray-900 dark:text-white">Otras Opciones</h5>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Configuración del sistema</p>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Geo Block -->
        <div class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div @class([
                    'w-10 h-10 rounded-xl flex items-center justify-center transition-colors',
                    'bg-green-50 text-green-600' => $isGeoBlockEnabled,
                    'bg-gray-50 text-gray-400' => !$isGeoBlockEnabled,
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h6 class="text-sm font-bold text-gray-800 dark:text-gray-200">Geo-protección</h6>
                    <p class="text-[10px] text-gray-500 uppercase tracking-tight">Solo acceso desde México</p>
                </div>
            </div>
            <button wire:click="toggleGeoBlock" @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-2 ring-offset-2 ring-transparent focus:ring-indigo-500',
                'bg-indigo-600' => $isGeoBlockEnabled,
                'bg-gray-200' => !$isGeoBlockEnabled,
            ])>
                <span @class([
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    'translate-x-6' => $isGeoBlockEnabled,
                    'translate-x-1' => !$isGeoBlockEnabled,
                ])></span>
            </button>
        </div>

        <!-- Txt Limits -->
        <div class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div @class([
                    'w-10 h-10 rounded-xl flex items-center justify-center transition-colors',
                    'bg-amber-50 text-amber-600' => $isTxtLimitsUnlocked,
                    'bg-gray-50 text-gray-400' => !$isTxtLimitsUnlocked,
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h6 class="text-sm font-bold text-gray-800 dark:text-gray-200">Desbloquear TxT</h6>
                    <p class="text-[10px] text-gray-500 uppercase tracking-tight">Omitir límites mensuales</p>
                </div>
            </div>
            <button wire:click="toggleTxtLimits" @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-2 ring-offset-2 ring-transparent focus:ring-indigo-500',
                'bg-amber-600' => $isTxtLimitsUnlocked,
                'bg-gray-200' => !$isTxtLimitsUnlocked,
            ])>
                <span @class([
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    'translate-x-6' => $isTxtLimitsUnlocked,
                    'translate-x-1' => !$isTxtLimitsUnlocked,
                ])></span>
            </button>
        </div>

        <!-- Chat -->
        <div class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div @class([
                    'w-10 h-10 rounded-xl flex items-center justify-center transition-colors',
                    'bg-blue-50 text-blue-600' => $isChatAllToAll,
                    'bg-gray-50 text-gray-400' => !$isChatAllToAll,
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <h6 class="text-sm font-bold text-gray-800 dark:text-gray-200">Chat Abierto</h6>
                    <p class="text-[10px] text-gray-500 uppercase tracking-tight">Interacción Todos-Todos</p>
                </div>
            </div>
            <button wire:click="toggleChat" @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-2 ring-offset-2 ring-transparent focus:ring-indigo-500',
                'bg-indigo-600' => $isChatAllToAll,
                'bg-gray-200' => !$isChatAllToAll,
            ])>
                <span @class([
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    'translate-x-6' => $isChatAllToAll,
                    'translate-x-1' => !$isChatAllToAll,
                ])></span>
            </button>
        </div>

        <!-- Maintenance -->
        <div class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div @class([
                    'w-10 h-10 rounded-xl flex items-center justify-center transition-colors',
                    'bg-red-50 text-red-600' => $isMaintenanceMode,
                    'bg-gray-50 text-gray-400' => !$isMaintenanceMode,
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h6 class="text-sm font-bold text-gray-800 dark:text-gray-200">Mantenimiento</h6>
                    <p class="text-[10px] text-gray-500 uppercase tracking-tight">Bloquear captura general</p>
                </div>
            </div>
            <button wire:click="toggleMaintenance" @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-2 ring-offset-2 ring-transparent focus:ring-indigo-500',
                'bg-red-600' => $isMaintenanceMode,
                'bg-gray-200' => !$isMaintenanceMode,
            ])>
                <span @class([
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    'translate-x-6' => $isMaintenanceMode,
                    'translate-x-1' => !$isMaintenanceMode,
                ])></span>
            </button>
        </div>
    </div>
</div>