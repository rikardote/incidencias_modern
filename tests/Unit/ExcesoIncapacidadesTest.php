<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcesoIncapacidadesTest extends TestCase
{
    /**
     * Test antiquity calculation returns integer years.
     */
    public function test_get_antiguedad_returns_integer_years()
    {
        // 1.5 years ago should return 1
        $date = Carbon::now()->subMonths(18)->toDateString();
        $this->assertEquals(1, getAntiguedad($date));

        // 0.9 years ago should return 0
        $date = Carbon::now()->subMonths(11)->toDateString();
        $this->assertEquals(0, getAntiguedad($date));

        // Exactly 5 years ago should return 5
        $date = Carbon::now()->subYears(5)->toDateString();
        $this->assertEquals(5, getAntiguedad($date));
    }

    /**
     * Test the day limits logic.
     * 0 years: > 15
     * 1-4 years: > 30
     * 5-9 years: > 45
     * 10+ years: > 60
     */
    public function test_get_excesode_incapacidad_logic()
    {
        // Case 0 years, 15 days (not excess)
        $this->assertEquals(0, getExcesodeIncapacidad(15, 0));
        // Case 0 years, 16 days (excess)
        $this->assertEquals(1, getExcesodeIncapacidad(16, 0));

        // Case 1 year, 30 days (not excess)
        // This is where the old bug was if it used >1 instead of >=1
        $this->assertEquals(0, getExcesodeIncapacidad(30, 1));
        // Case 1 year, 31 days (excess)
        $this->assertEquals(1, getExcesodeIncapacidad(31, 1));

        // Case 4 years, 30 days (not excess)
        $this->assertEquals(0, getExcesodeIncapacidad(30, 4));
        // Case 4 years, 31 days (excess)
        $this->assertEquals(1, getExcesodeIncapacidad(31, 4));

        // Case 5 years, 45 days (not excess)
        $this->assertEquals(0, getExcesodeIncapacidad(45, 5));
        // Case 5 years, 46 days (excess)
        $this->assertEquals(1, getExcesodeIncapacidad(46, 5));

        // Case 10 years, 60 days (not excess)
        $this->assertEquals(0, getExcesodeIncapacidad(60, 10));
        // Case 10 years, 61 days (excess)
        $this->assertEquals(1, getExcesodeIncapacidad(61, 10));
    }

    /**
     * Test floating year period calculation.
     */
    public function test_floating_year_period()
    {
        // Assume hire date is 2010-05-15
        $hireDate = '2010-05-15';

        // Mock "now" to be 2024-03-10 (before anniversary)
        Carbon::setTestNow(Carbon::create(2024, 3, 10));
        $periodStart = getdateActual($hireDate);
        // Should be 2023-05-15 (last anniversary)
        $this->assertEquals('2023-05-15', $periodStart);

        $periodEnd = getdatePosterior($periodStart);
        // 2024-05-14 (day before next anniversary)
        $this->assertEquals('2024-05-14', $periodEnd);

        // Mock "now" to be 2024-06-10 (after anniversary)
        Carbon::setTestNow(Carbon::create(2024, 6, 10));
        $periodStart = getdateActual($hireDate);
        // Should be 2024-05-15 (latest anniversary)
        $this->assertEquals('2024-05-15', $periodStart);

        Carbon::setTestNow(); // Reset mocked time
    }
}