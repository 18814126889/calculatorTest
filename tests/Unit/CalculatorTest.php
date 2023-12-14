<?php

namespace Tests\Unit;

use App\Services\CalculatorService;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_verify_is_true(): void
    {
        $calculator = new CalculatorService('1+1');

        $output = $calculator->verify();
        $this->assertTrue(true, $output);
    }

    /**
     * A basic test example.
     */
    public function test_Calculator_is_true(): void
    {
        $calculator = new CalculatorService('1+1');

        $output = $calculator->calculate();
        $this->assertEquals(2, $output);
    }
}
