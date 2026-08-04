<?php

namespace App\Support;

use DateTimeInterface;
use NumberFormatter;

class BrazilFormat
{
    public static function money(float|int|string|null $value): string
    {
        if (! class_exists(NumberFormatter::class)) {
            return 'R$ '.number_format((float) ($value ?? 0), 2, ',', '.');
        }

        $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency((float) ($value ?? 0), 'BRL') ?: 'R$ 0,00';
    }

    public static function number(float|int|string|null $value): string
    {
        return number_format((float) ($value ?? 0), 0, ',', '.');
    }

    public static function dateTime(?DateTimeInterface $value): string
    {
        return $value ? $value->format('d/m/Y H:i') : '-';
    }
}
