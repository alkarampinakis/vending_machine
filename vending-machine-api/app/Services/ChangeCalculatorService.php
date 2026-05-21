<?php

namespace App\Services;

class ChangeCalculatorService
{
    private static array $coins = [100, 50, 20, 10, 5];

    public static function calculate(int $amount): array
    {
        $result = [];
        foreach (self::$coins as $coin) {
            if ($amount >= $coin) {
                $result[$coin] = intdiv($amount, $coin);
                $amount %= $coin;
            }
        }
        return $result;
    }
}
