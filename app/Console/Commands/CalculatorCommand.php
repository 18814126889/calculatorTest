<?php

namespace App\Console\Commands;

use App\Exceptions\CalculatorException;
use App\Services\CalculatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\text;

class CalculatorCommand extends Command
{

    protected $signature = 'calculator';

    protected $description = '简单计算器';

    protected string $expression;

    public function handle()
    {
        $expression = text(
            label: 'Please enter expression',
            required: 'The expression is required.',
            validate: fn (string $expression) => match (true) {
                !preg_match('/^[\d+\-*\/.()]+$/', $expression) => 'Please enter the correct expression',
                default => null
            }
        );

        try {
            $calculator = new CalculatorService($expression);
            $output = $calculator->calculate();
            $this->info($output);

        } catch (CalculatorException $calculatorException) {
            Log::error($calculatorException->getMessage());
            $this->error($calculatorException->getMessage());

        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            throw $throwable;
        }

    }

}
