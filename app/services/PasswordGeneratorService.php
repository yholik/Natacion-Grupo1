<?php

// Genera contraseñas provisorias para altas creadas por admin.

class PasswordGeneratorService
{
    // Devuelve una clave aleatoria usando un alfabeto acotado.
    public function generate(int $length = 12): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!#$%?@';
        $password = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $maxIndex)];
        }

        return $password;
    }
}
