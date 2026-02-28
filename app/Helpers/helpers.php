<?php
use Carbon\Carbon;
use App\Models\User;
use App\Models\Qna;
use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\CodigoDeIncidencia;
use App\Models\Department;
use App\Models\Configuration;
use App\Models\Checada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


function genToken()
{
    $length = 64;

    $characters = '0123456789abcdefghijk@#$%lmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return hash('sha1', $randomString . time());
}

function capturado_por($id)
{
    $user = User::find($id);
    if ($user) {
        $notocar = array('del', 'de');
        $trozos = explode(' ', $user->name);
        $iniciales = [];
        foreach ($trozos as $trozo) {
            if (in_array(mb_strtolower($trozo), $notocar)) {
                $iniciales[] = $trozo;
            }
            else {
                $iniciales[] = mb_strtoupper(mb_substr($trozo, 0, 1)) . ".";
            }
        }
        return implode(' ', $iniciales);
    }
    return "";
}

function fecha_ymd($date)
{
    return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
}
function fecha_dmy($date)
{
    return date('d/m/Y', strtotime(str_replace('/', '-', $date)));
}
function fecha_dmy_hora($date)
{
    if (!is_null($date)) {
        date_default_timezone_set("America/Los_Angeles");
        $date = strtotime($date . ' UTC');
        $date = date("Y-m-d H:i:s", $date);

        $fecha = date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $date)));
        date_default_timezone_set("UTC");
        return $fecha;
    }
}
function fecha_dmy_hora_los_angeles($date)
{
    if (!is_null($date)) {
        $fecha = date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $date)));
        return $fecha;
    }
}
function qna_year($fecha)
{
    $date = Carbon::parse($fecha);
    $month = $date->month;
    $day = $date->day;
    $year = $date->year;

    $qnaNum = $month * 2;
    if ($day < 16) {
        $qnaNum -= 1;
    }

    // Primero intentamos con quincenas activas (caso normal)
    $qna = Qna::where('qna', $qnaNum)->where('year', $year)->where('active', '1')->first();

    if ($qna) {
        return $qna->id;
    }

    // Si no hay QNA activa, verificamos si el usuario tiene un pase para ESA quincena específica
    $user = auth()->user();
    if ($user) {
        $qnaCerrada = Qna::where('qna', $qnaNum)->where('year', $year)->first();
        if ($qnaCerrada && $user->canCaptureInClosedQna($qnaCerrada->id)) {
            return $qnaCerrada->id;
        }
    }

    return false;
}
function getFechaInicioPorQna($qna_id)
{
    $qna = Qna::where('id', '=', $qna_id)->first();

    $mes = ($qna->qna / 2);
    $mes_redondeado = ceil($mes);
    $mes_int = intval($mes_redondeado);

    if ($qna->qna % 2 == 0) {
        $f_inicio = Carbon::create($qna->year, $mes_int, '16', 0, 0, 0);
    }
    else {
        $f_inicio = Carbon::create($qna->year, $mes_int, '01', 0, 0, 0);
    }

    return $f_inicio->toDateString();

}
function getFechaFinalPorQna($f_inicio)
{
    $date = Carbon::parse($f_inicio);
    $month = $date->month;
    $day = $date->day;
    $year = $date->year;

    if ($day < 16) {
        $f_final = Carbon::create($year, $month, '15', 0, 0, 0);
    }
    else {
        $f_final = $date->endOfMonth();
    }
    return $f_final->toDateString();

}
function getDoctor($medico_id)
{
    $doctor = Employe::where('id', '=', $medico_id)->first();
    //if($doctor) ? $doctor->father_lastname .' '. $doctor->mother_lastname .' '. $doctor->name : return null;
    if ($doctor) {
        return $doctor->father_lastname . ' ' . $doctor->mother_lastname . ' ' . $doctor->name;
    }
    else {
        return null;
    }

}

function getdateActual($fecha_ingreso)
{
    $f_ingreso = Carbon::parse($fecha_ingreso);
    $año_ingreso = $f_ingreso->year;
    $mes_ingreso = $f_ingreso->month;
    $dia_ingreso = $f_ingreso->day;

    $today = carbon::now();
    $año_now = $today->year;
    $mes_now = $today->month;
    $dia_now = $today->day;

    $f_ingreso_actual = Carbon::create($año_now, $mes_ingreso, $dia_ingreso, 0, 0, 0);
    $f_ingreso_ano_anterior = Carbon::create(($año_now - 1), $mes_ingreso, $dia_ingreso, 0, 0, 0);

    if ($f_ingreso_actual->lte($today)) {
        return $f_ingreso_actual->toDateString();
    }
    else {
        return $f_ingreso_ano_anterior->toDateString();
    }

}
function getdatePosterior($date)
{
    $dt = Carbon::parse($date);

    return Carbon::parse($date)->addYear()->subDay()->toDateString();
}
function getAntiguedad($date)
{
    $dt = Carbon::parse($date);
    $año = $dt->year;
    $mes = $dt->month;
    $dia = $dt->day;

    return (int)Carbon::parse($date)->diffInYears(Carbon::now());
}
function getExcesodeIncapacidad($dias_lic, $antiguedad)
{
    switch (true) {
        case ($dias_lic > 15 && $antiguedad < 1):
            return 1;
        case ($dias_lic > 30 && $antiguedad >= 1 && $antiguedad <= 4):
            return 1;
        case ($dias_lic > 45 && $antiguedad >= 5 && $antiguedad <= 9):
            return 1;
        case ($dias_lic > 60 && $antiguedad >= 10):
            return 1;
        default:
            return 0;
    }

}
function getExcesodeLicenciasConGoce($fecha_inicial, $antiguedad, $num_empleado, $total_dias)
{
    //$dt = Carbon::now();
    //$año = $dt->year;
    $dt = Carbon::parse($fecha_inicial);
    $año = $dt->year;
    $fecha_inicio = $año . '-01-01';
    $fecha_final = $año . '-12-31';

    $lic = Incidencia::getTotalLicencias($num_empleado, $fecha_inicio, $fecha_final);
    $total_lic = $lic + $total_dias;

    if ($total_lic > 21 && $antiguedad <= 4) {
        return $lic . ' de 21';
    }
    elseif ($total_lic > 26 && $antiguedad >= 5 && $antiguedad <= 9) {
        return $lic . ' de 26';
    }
    elseif ($total_lic > 31 && $antiguedad >= 10 && $antiguedad <= 14) {
        return $lic . ' de 31';
    }
    elseif ($total_lic > 36 && $antiguedad >= 15 && $antiguedad <= 19) {
        return $lic . ' de 36';
    }
    elseif ($total_lic > 41 && $antiguedad >= 20) {
        return $lic . ' de 41';
    }
    else {
        return 0;
    }

}

function getTxtPorMes($num_empleado, $date)
{

    $primer_dia = Carbon::parse($date)->startOfMonth();
    $ultimo_dia = Carbon::parse($date)->lastOfMonth();
    $total_txt = Incidencia::Gettxtpormes($primer_dia, $ultimo_dia, $num_empleado);
    return $total_txt;
}


function getMonth($date)
{

    $dt = Carbon::parse($date);
    $mes = $dt->month;
    $mes = date("F", mktime(0, 0, 0, $mes, 10));
    switch ($mes) {
        case 'January':
            $mes = "ENERO";
            break;
        case 'February':
            $mes = "FEBRERO";
            break;
        case 'March':
            $mes = "MARZO";
            break;
        case 'April':
            $mes = "ABRIL";
            break;
        case 'May':
            $mes = "MAYO";
            break;
        case 'June':
            $mes = "JUNIO";
            break;
        case 'July':
            $mes = "JULIO";
            break;
        case 'August':
            $mes = "AGOSTO";
            break;
        case 'September':
            $mes = "SEPTIEMBRE";
            break;
        case 'October':
            $mes = "OCTUBRE";
            break;
        case 'November':
            $mes = "NOVIEMBRE";
            break;
        case 'December':
            $mes = "DICIEMBRE";
            break;
    }
    return $mes . ' DE ' . $año = $dt->year;
    ;
}
function array_group_by($arr, $key)
{
    if (!is_array($arr)) {
        trigger_error('array_group_by(): The first argument should be an array', E_USER_ERROR);
    }
    if (!is_string($key) && !is_int($key) && !is_float($key)) {
        trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
    }
    // Load the new array, splitting by the target key
    $grouped = [];
    foreach ($arr as $value) {
        $grouped[$value[$key]][] = $value;
    }
    // Recursively build a nested grouping if more parameters are supplied
    // Each grouped array value is grouped according to the next sequential key
    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $key => $value) {
            $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $parms);
        }
    }
    return $grouped;
}
function array_split_value($array)
{
    $result = array();
    $indexes = array();

    foreach ($array as $key => $value) {
        if (!in_array($value, $indexes)) {
            $indexes[] = $value;
            $result[] = array($key => $value);
        }
        else {
            $index_search = array_search($value, $indexes);
            $result[$index_search] = array_merge($result[$index_search], array($key => $value));
        }
    }

    return $result;
}
function getFechaCierre()
{
    try {
        $qna = Qna::where('active', '=', 1)->first();
        if (!$qna || !$qna->cierre) {
            return 'N/A';
        }

        $dt = Carbon::parse($qna->cierre);
        $dia = $dt->day;
        $año = $dt->year;

        // Obtener el mes en texto en inglés y luego traducirlo
        $mes = $dt->format('F'); // Usar format('F') en lugar de monthName

        // Traducción de meses a español
        $meses_es = [
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'septiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre'
        ];

        if (isset($meses_es[$mes])) {
            $mes = $meses_es[$mes];
        }

        return "$dia de $mes de $año";
    }
    catch (\Exception $e) {
        return 'N/A';
    }
}

function checkWeekdays($fecha_inicio, $fecha_final)
{
    $date1 = new DateTime($fecha_inicio);
    $date2 = new DateTime($fecha_final);

    $diff = date_diff($date1, $date2);
    if ($diff->days >= 10) { //Mathematically, there will ALWAYS be either a Friday or Saturday in any given span of 6 consecutive days.
    //Warkwark
    }
    else {
        $range = range($date1->format("w"), $date1->format("w") + $diff->days); // [5,6,7,8,9]
        array_walk($range, function (&$a, $b) {
            $a = $a % 7;
        }); // [5,6,0,1,2]
        if (in_array(6, $range) || in_array(0, $range)) { // 5 for Friday, 6 for Saturday.
            return true;
        }
        else {
            return false;
        }
    }

}
function getDeparment($depa_id)
{
    $departamento = Department::where('id', $depa_id)->first();
    return $departamento->code;
}

function valida_entrada($num_empleado, $fecha, $entrada)
{
    $empleado = Employe::get_empleado($num_empleado);
    $empleado_entrada = substr($empleado->horario, 0, 5);
    $empleado_salida = substr($empleado->horario, 8);
    $fecha = fecha_ymd($fecha);


    //$entrada = "07:10";
    /*$horario_in=$empleado_entrada.":00";
     //$horario_out=$horariosalida_checadas.":00";
     $st_time =   strtotime($empleado_entrada);
     //$end_time   =   strtotime($empleado_salida);
     $checkin =   strtotime($entrada);
     //$checkout =   strtotime($checkout_checadas);
     */
    $minutoAnadir = 10;

    $segundos_horaInicial = strtotime($empleado_entrada);

    $segundos_minutoAnadir = $minutoAnadir * 60;

    $nuevaHora = date("H:i:sa", $segundos_horaInicial + $segundos_minutoAnadir);

    $entrada_comp = date('h:i:s A', strtotime($empleado_entrada . " +4 hours"));


    //dd($nuevaHora);

    $incidencia = Incidencia::where('employee_id', '=', $empleado->emp_id)
        ->whereRaw('? between fecha_inicio and fecha_final', [$fecha])
        ->whereNotIn('codigodeincidencia_id', [41, 15, 81])
        ->first();


    if ($entrada >= $entrada_comp) {
        if ($incidencia) {
            $code = CodigoDeIncidencia::find($incidencia->codigodeincidencia_id);
            return "(" . $code->code . ")";
        }

    }
    if ($incidencia) {
        $code = CodigoDeIncidencia::find($incidencia->codigodeincidencia_id);
        if (!$entrada) {
            return $code->code;
        }
    }
    if ($entrada >= $nuevaHora && $incidencia) {
        return "<b><font  color='red'>" . $entrada . "</font></b> (" . $code->code . ")";

    }
    if ($entrada >= $nuevaHora && !$incidencia) {
        return "<b><font  color='red'>" . $entrada . "</font></b>";

    }

    return $entrada;

}
function valida_salida($num_empleado, $fecha, $salida, $entrada)
{
    $empleado = Employe::get_empleado($num_empleado);
    $empleado_entrada = substr($empleado->horario, 0, 5);
    $empleado_salida = substr($empleado->horario, 8);
    $fecha = fecha_ymd($fecha);

    //$salida_comp = date('h:i:s A', strtotime($empleado_entrada. " +4 hours"));


    $incidencia = Incidencia::where('employee_id', '=', $empleado->emp_id)
        ->whereRaw('? between fecha_inicio and fecha_final', [$fecha])
        ->whereNotIn('codigodeincidencia_id', [1, 10, 30, 43, 7, 81])
        ->first();
    if ($incidencia) {
        $code = CodigoDeIncidencia::find($incidencia->codigodeincidencia_id);
    }

    if ($incidencia && $salida) {
        return $salida . "(" . $code->code . ")";
    }
    if (!$incidencia && $entrada == $salida) {
        return "";
    }
    if (!$incidencia && $salida) {
        return $salida;
    }



    if ($incidencia) {
        return $code->code;
    }

}

function get_departamento($deparment_id)
{

    $dpto = Department::find($deparment_id);
    if ($dpto->code == "00104")
        return "00105";
    else
        return $dpto->code;

}
function validar_entrada($num_empleado, $checada)
{
    $empleado = Employe::get_empleado($num_empleado);
    $hora_de_entrada = substr($empleado->horario, 0, 5);
    $hora_de_salida = substr($empleado->horario, 8);


    $es_entrada = FALSE;
    $es_salida = FALSE;

    //$checadas1 = $checadas->min('hora');
    //$checadas2 = $checadas->max('hora');

    $entrada_comp = date('h:i:s A', strtotime($hora_de_entrada . " +4 hours"));

    if ($checada < $entrada_comp) {
        $es_entrada = TRUE;
        return $checada;
    }
    else
        return "";
/*
 if ($checadas2 > $entrada_comp) {
 $es_salida = TRUE;
 }
 if ($es_entrada && $es_salida) {
 $a = $checadas1.' - '.$checadas2;
 }
 if ($es_entrada && !$es_salida) {
 $a = $checadas1.' - '."";
 }
 if (!$es_entrada && $es_salida) {
 $a = "".' - '.$checadas2;
 }
 */


}
function validar_salida($num_empleado, $checada)
{
    $empleado = Employe::get_empleado($num_empleado);
    $hora_de_entrada = substr($empleado->horario, 0, 5);
    $hora_de_salida = substr($empleado->horario, 8);


    $es_entrada = FALSE;
    $es_salida = FALSE;

    //$checadas1 = $checadas->min('hora');
    //$checadas2 = $checadas->max('hora');

    $entrada_comp = date('h:i:s A', strtotime($hora_de_entrada . " +4 hours"));

    if ($checada > $entrada_comp) {
        $es_salida = TRUE;
        return $checada;
    }
    else
        return "";
/*
 if ($checadas2 > $entrada_comp) {
 $es_salida = TRUE;
 }
 if ($es_entrada && $es_salida) {
 $a = $checadas1.' - '.$checadas2;
 }
 if ($es_entrada && !$es_salida) {
 $a = $checadas1.' - '."";
 }
 if (!$es_entrada && $es_salida) {
 $a = "".' - '.$checadas2;
 }
 */


}
function validar_licencia_sin_goce($empleado_id, $fecha_inicial, $fecha_final)
{

    //$lic = Incidencia::getTotalLicenciasSinGoce($empleado_id,$fecha_inicial,$fecha_final);
    $dt = Carbon::parse($fecha_inicial);
    $dt = $dt->addMonths(6);
    $lic = Incidencia::orderBy('incidencias.fecha_final', 'desc')->where('incidencias.codigodeincidencia_id', '=', 40)->where('incidencias.employee_id', '=', $empleado_id)->first();

    if ($lic) {
        $dt2 = Carbon::parse($lic->fecha_final);
        $dias = $dt->diffInHours($dt2);
        dd($dias);
    }

}

function check_manto()
{
    try {
        $mantenimiento = DB::table('configurations')
            ->where('name', 'mantenimiento')
            ->first();
        if ($mantenimiento && data_get($mantenimiento, 'state')) {
            return true;
        }
    }
    catch (\Exception $e) {
        Log::error('Error en check_manto: ' . $e->getMessage());
    }
    return false;
}
function check_entrada($fecha, $num_empleado)
{
    $entrada = Checada::where('num_empleado', $num_empleado)
        ->where('fecha', 'LIKE', '%' . $fecha . '%')
        ->orderBy('fecha', 'asc')
        ->first();
    if ($entrada && strtotime($entrada->fecha) < strtotime('12:30')) {
        //if($entrada){
        return date("H:i", strtotime($entrada->fecha));
    }
    else {
        return "";
    }



}
function check_salida($fecha, $num_empleado, $entrada)
{

    $salida = Checada::where('num_empleado', $num_empleado)
        ->where('fecha', 'LIKE', '%' . $fecha . '%')
        ->orderBy('fecha', 'desc')
        ->first();
    if ($salida) {
        //if($entrada){
        return date("H:i", strtotime($salida->fecha));
    }
    else {
        return "";
    }

}

function isweekend($date)
{
    $date = Carbon::parse($date);

    if ($date->isWeekend()) {
        return "true";
    }
}

function getDia($date)
{
    $dt = Carbon::parse($date);
    $dia = $dt->day;
    switch ($dt->dayOfWeek) {
        case 1:
            return 'Lun ' . $dia;
        case 2:
            return 'Mar ' . $dia;
        case 3:
            return 'Mie ' . $dia;
        case 4:
            return 'Jue ' . $dia;
        case 5:
            return 'Vie ' . $dia;
        case 6:
            return 'SAB ' . $dia;
        case 7:
            return 'DOM ' . $dia;
    }


}