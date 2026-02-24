<?php

namespace App\Constants;

/**
 * Constantes de dominio para Incidencias
 * -------------------------------------
 * Centraliza códigos, jornadas y reglas base
 * para evitar magic numbers y bugs silenciosos.
 */
class Incidencias
{
    /*
    |--------------------------------------------------------------------------
    | CÓDIGOS DE INCIDENCIA
    |--------------------------------------------------------------------------
    */

    // Faltas
    const FALTA = 10;

    // Incapacidades (Tipos de incapacidades médicas)
    const INCAPACIDADES = [53, 54, 55];

    // Licencias con goce
    const LICENCIAS = [40, 41, 47, 48, 49];

    // Vacaciones
    const VACACIONES = [60, 62, 63];

    // TXT
    const TXT = 900;

    // Pase de salida
    const PASE_SALIDA = 905;

    // Código obsoleto
    const CODIGO_OBSOLETO = 912;

    /*
    |--------------------------------------------------------------------------
    | CÓDIGOS QUE PUEDEN COEXISTIR (DUPLICABLES)
    |--------------------------------------------------------------------------
    */

    // Estos códigos SÍ pueden capturarse juntos en el mismo periodo
    const DUPLICABLES = [
        1, 18, 19, 2, 3, 4, 7,
        25, 30,
        92, 93,
        905
    ];

    /*
    |--------------------------------------------------------------------------
    | JORNADAS
    |--------------------------------------------------------------------------
    */

    // Jornadas de guardias
    const JORNADA_GUARDIAS = [
        2, 3, 5, 6, 18, 13, 20,
        4, 7, 8, 9, 10, 11, 19,
        21, 22, 23, 24, 25, 26,
        27, 28, 29, 30, 31, 32, 34
    ];

    // Subgrupos de guardias para límites vacacionales
    const JORNADA_VAC_5_DIAS = [2, 3, 5, 6, 18, 13, 20, 30, 32, 34];
    const JORNADA_VAC_6_DIAS = [4, 7, 8, 9, 10, 11, 19, 31];

    // Sistema y Fin / Día y Fin
    const JORNADA_SYF_DYF = [1, 15];

    // Matutino / Vespertino / Desfasado
    const JORNADA_MAT_DESP = [14, 17];

    /*
    |--------------------------------------------------------------------------
    | REGLAS DE DÍAS POR JORNADA
    |--------------------------------------------------------------------------
    */

    // Días que se contabilizan por tipo de jornada
    const DIAS_GUARDIAS = 2;
    const DIAS_SYF_DYF = 4;

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE APOYO (LEGIBLES)
    |--------------------------------------------------------------------------
    */

    /**
     * Indica si un código es vacacional
     */
    public static function esVacacional($codigo)
    {
        return in_array($codigo, self::VACACIONES);
    }

    /**
     * Indica si un código es de licencia con goce de sueldo
     */
    public static function esLicencia($codigo)
    {
        return in_array($codigo, self::LICENCIAS);
    }

    /**
     * Indica si un código corresponde a una incapacidad médica
     */
    public static function esIncapacidad($codigo)
    {
        return in_array($codigo, self::INCAPACIDADES);
    }

    /**
     * Indica si un código es un pase de salida
     */
    public static function esPaseSalida($codigo)
    {
        return $codigo == self::PASE_SALIDA;
    }

    /**
     * Indica si un código puede coexistir con otros
     */
    public static function esDuplicable($codigo)
    {
        return in_array($codigo, self::DUPLICABLES);
    }

    /**
     * Determina si la jornada es de guardias
     */
    public static function esGuardia($jornada_id)
    {
        return in_array($jornada_id, self::JORNADA_GUARDIAS);
    }

    /**
     * Determina si la jornada es SYF / DYF
     */
    public static function esSyfDyf($jornada_id)
    {
        return in_array($jornada_id, self::JORNADA_SYF_DYF);
    }

    /**
     * Obtiene días ajustados por jornada
     */
    public static function diasPorJornada($jornada_id)
    {
        if (self::esGuardia($jornada_id)) {
            return self::DIAS_GUARDIAS;
        }

        if (self::esSyfDyf($jornada_id)) {
            return self::DIAS_SYF_DYF;
        }

        return null;
    }
}
