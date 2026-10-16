<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PartitaIva implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = trim($value);

        if (!preg_match('/^\d{11}$/', $digits)) {
            $fail('La Partita IVA deve essere di esattamente 11 cifre.');
            return;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $d = (int) $digits[$i];
            if ($i % 2 === 1) {
                $d *= 2;
                if ($d > 9) $d -= 9;
            }
            $sum += $d;
        }

        $check = (10 - ($sum % 10)) % 10;

        if ($check !== (int) $digits[10]) {
            $fail('La Partita IVA inserita non è valida.');
        }
    }
}
