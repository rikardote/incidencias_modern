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
                $response = Http::get("{$this->baseUrl}/employees/search", [
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

    /**
     * Preload multiple employees data into cache concurrently using Http::pool.
     * This avoids N+1 API sequential requests when rendering lists.
     * 
     * @param array $numerosEmpleado
     * @return void
     */
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
                        $requests[] = $pool->as("emp_{$num}")->get("{$this->baseUrl}/employees/search", [
                            'id_empleado' => $num,
                            'latest' => true
                        ]);
                    }
                    return $requests;
                });

                // Guardar las respuestas exitosas en caché usando la misma clave que getEmployeeData
                foreach ($responses as $key => $response) {
                    if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                        $num = str_replace('emp_', '', $key);
                        $data = $response->json();
                        
                        if (!empty($data)) {
                            $storeData = is_array($data) && isset($data[0]) ? $data[0] : $data;
                            Cache::put("employee_api_data_{$num}", $storeData, 3600);
                        } else {
                            // Para evitar golpear la API constantemente si el empleado no existe
                            Cache::put("employee_api_data_{$num}", [], 3600);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error in preloadEmployeesData: " . $e->getMessage());
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
