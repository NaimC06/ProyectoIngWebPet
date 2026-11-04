<?php

namespace App\Validator;

final class CedulaEcuatoriana
{
    public static function esValida(?string $cedula): bool
    {
        if (
            $cedula === null
            || preg_match('/^[0-9]{10}$/D', $cedula) !== 1
        ) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        $tercero = (int) $cedula[2];

        if ($provincia < 1 || $provincia > 24 || $tercero > 5) {
            return false;
        }

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $producto = (int) $cedula[$i] * $coeficientes[$i];

            if ($producto > 9) {
                $producto -= 9;
            }

            $suma += $producto;
        }

        $verificador = (10 - ($suma % 10)) % 10;

        return $verificador === (int) $cedula[9];
    }
}