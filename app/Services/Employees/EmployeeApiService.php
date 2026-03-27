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
                        // Con latest=true, la API devuelve el objeto directamente o un array de un solo item.
                        return is_array($data) && isset($data[0]) ? $data[0] : $data;
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error connecting to Employees API: " . $e->getMessage());
            }

            return null;
        });
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
