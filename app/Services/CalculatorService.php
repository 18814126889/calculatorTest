<?php

namespace App\Services;

use App\Exceptions\CalculatorException;

class CalculatorService
{
    protected string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * 正则校验
     * @return bool
     * @throws CalculatorException
     */
    public function verify(): bool
    {
        return (bool) preg_match('/^[\d+\-*\/.()]+$/', $this->expression);
    }

    /**
     * @return false|mixed
     * @throws CalculatorException
     */
    public function calculate()
    {
        $this->expression = str_replace(' ', '', $this->expression);

        $numStack = [];
        $opStack = [];

        $n = strlen($this->expression);

        for ($i = 0; $i < $n; $i++) {
            $char = $this->expression[$i];

            if ($char >= '0' && $char <= '9') {
                $num = 0;
                while ($i < $n && $this->expression[$i] >= '0' && $this->expression[$i] <= '9') {
                    $num = $num * 10 + (int)$this->expression[$i];
                    $i++;
                }
                array_push($numStack, $num);
                $i--;
            } elseif ($char === '+' || $char === '-' || $char === '*' || $char === '/') {
                while (!empty($opStack) && $this->hasPrecedence($char, end($opStack))) {
                    $this->performOperation($numStack, $opStack);
                }
                array_push($opStack, $char);
            } elseif ($char === '(') {
                array_push($opStack, $char);
            } elseif ($char === ')') {
                while (!empty($opStack) && end($opStack) !== '(') {
                    $this->performOperation($numStack, $opStack);
                }
                array_pop($opStack);
            }
        }

        while (!empty($opStack)) {
            $this->performOperation($numStack, $opStack);
        }

        return end($numStack);
    }

    /**
     * @param $op1
     * @param $op2
     * @return bool
     */
    protected function hasPrecedence($op1, $op2): bool
    {
        if ($op2 === '(' || $op2 === ')') {
            return false;
        }
        if (($op1 === '*' || $op1 === '/') && ($op2 === '+' || $op2 === '-')) {
            return false;
        }
        return true;
    }

    /**
     * @param $numStack
     * @param $opStack
     * @return void
     * @throws CalculatorException
     */
    protected function performOperation(&$numStack, &$opStack): void
    {
        $op = array_pop($opStack);
        $num2 = array_pop($numStack);
        $num1 = array_pop($numStack);

        switch ($op) {
            case '+':
                array_push($numStack, $num1 + $num2);
                break;
            case '-':
                array_push($numStack, $num1 - $num2);
                break;
            case '*':
                array_push($numStack, $num1 * $num2);
                break;
            case '/':
                if ($num2 != 0) {
                    array_push($numStack, $num1 / $num2);
                } else {
                    throw new CalculatorException('除数不能为零');
                }
                break;
        }
    }
}
