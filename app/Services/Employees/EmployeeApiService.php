<?php

namespace App\Services\Employees;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EmployeeApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.employees.api_url');
    }

    /**
     * Get detailed data for a specific employee by their employee number.
     * 
     * @param string $numEmpleado
     * @return array|null
     */
    public function getEmployeeData($numEmpleado)
    {
        return Cache::remember("employee_api_data_{$numEmpleado}", 3600, function () use ($numEmpleado) {
            try {
                $response = Http::timeout(3)->get("{$this->baseUrl}/employees/search", [
                    'id_empleado' => $numEmpleado,
                    'latest' => true
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data)) {
                        return is_array($data) && isset($data[0]) ? $data[0] : $data;
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error connecting to Employees API: " . $e->getMessage());
            }

            return [];
        });
    }

    public function preloadEmployeesData(array $numerosEmpleado)
    {
        $missing = [];
        
        // Determinar cuáles números NO están en caché aún
        foreach (array_unique($numerosEmpleado) as $num) {
            if (empty($num)) continue;
            if (!Cache::has("employee_api_data_{$num}")) {
                $missing[] = $num;
            }
        }

        if (count($missing) > 0) {
            try {
                $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($missing) {
                    $requests = [];
                    foreach ($missing as $num) {
                        $requests[] = $pool->as("emp_{$num}")->timeout(3)->get("{$this->baseUrl}/employees/search", [
                            'id_empleado' => $num,
                            'latest' => true
                        ]);
                    }
                    return $requests;
                });

                // Guardar las respuestas en caché usando la misma clave que getEmployeeData
                foreach ($responses as $key => $response) {
                    $num = str_replace('emp_', '', $key);
                    
                    if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                        $data = $response->json();
                        
                        if (!empty($data)) {
                            $storeData = is_array($data) && isset($data[0]) ? $data[0] : $data;
                            Cache::put("employee_api_data_{$num}", $storeData, 3600);
                        } else {
                            // Para evitar golpear la API constantemente si el empleado no existe
                            Cache::put("employee_api_data_{$num}", [], 3600);
                        }
                    } else {
                        // Si la petición falla (timeout, error 500, no resuelve host), guardamos un arreglo vacío
                        // para evitar que el fallback secuencial intente hacer peticiones de nuevo.
                        Cache::put("employee_api_data_{$num}", [], 300); // Caché de error por 5 minutos
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error in preloadEmployeesData: " . $e->getMessage());
                // En caso de fallo total del pool, marcamos todos como fallidos temporalmente
                foreach ($missing as $num) {
                    Cache::put("employee_api_data_{$num}", [], 300);
                }
            }
        }
    }

    /**
     * Get all payroll history for a specific employee.
     */
    public function getPayrollHistory($numEmpleado)
    {
        return Cache::remember("employee_api_history_{$numEmpleado}", 3600, function () use ($numEmpleado) {
            try {
                $response = Http::get("{$this->baseUrl}/employees/search", [
                    'id_empleado' => $numEmpleado
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Log::error("Error connecting to Employees API for history: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Search for employees in the external system.
     */
    public function searchEmployees($query)
    {
        try {
            $response = Http::get("{$this->baseUrl}/employees/search", [
                'name' => $query
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error("Error searching Employees API: " . $e->getMessage());
        }

        return [];
    }
}
