<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employe;
use App\Services\Notifications\TelegramService;
use Illuminate\Support\Facades\Cache;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\Handlers\MenuHandler;
use App\Telegram\Handlers\AttendanceHandler;
use App\Telegram\Handlers\VacationHandler;
use App\Telegram\Handlers\EconomicDaysHandler;
use App\Telegram\Handlers\ManualPunchHandler;

use App\Telegram\Handlers\ExitPassHandler;

use App\Telegram\Handlers\GeneralIncidenceHandler;
use App\Telegram\Handlers\PayrollHandler;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $telegram)
    {
        $update = $request->all();
        
        // Determinar Chat ID
        $chatId = null;
        $text = '';
        
        if (isset($update['callback_query'])) {
            $chatId = $update['callback_query']['message']['chat']['id'];
        } elseif (isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'] ?? '';
        }

        if (!$chatId) {
            return response()->json(['status' => 'ok']);
        }

        // Datos del Usuario
        $employee = Employe::where('telegram_chat_id', $chatId)->first();
        $adminIds = explode(',', config('services.telegram.admin_ids', '1502287926'));
        $isAdmin = in_array((string)$chatId, $adminIds);
        
        $state = Cache::get('tg_state_' . $chatId);

        // Inicializar Handlers
        $handlers = [
            new StartHandler($telegram, $chatId),
            new AttendanceHandler($telegram, $chatId),
            new VacationHandler($telegram, $chatId),
            new EconomicDaysHandler($telegram, $chatId),
            new ExitPassHandler($telegram, $chatId),
            new GeneralIncidenceHandler($telegram, $chatId),
            new PayrollHandler($telegram, $chatId),
            new ManualPunchHandler($telegram, $chatId),
            new MenuHandler($telegram, $chatId),
        ];

        // 1. Manejar Flujo de Estado (Sessions)
        if ($state && isset($state['action'])) {
            foreach ($handlers as $handler) {
                if ($handler->handleState($text, $state, $employee, $isAdmin)) {
                    return response()->json(['status' => 'ok']);
                }
            }
        }

        // 2. Manejar Callbacks
        if (isset($update['callback_query'])) {
            $data = $update['callback_query']['data'];
            foreach ($handlers as $handler) {
                if ($handler->handleCallback($data, $employee, $isAdmin)) {
                    return response()->json(['status' => 'ok']);
                }
            }
        }

        // 3. Manejar Comandos
        if ($text) {
            foreach ($handlers as $handler) {
                if ($handler->handleCommand($text, $employee, $isAdmin)) {
                    return response()->json(['status' => 'ok']);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
