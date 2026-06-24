<?php

// Carga el .env y expone sus valores al resto de la app.

class Env
{

    // Lee el archivo línea por línea y publica cada variable.
    public static function load($path)
    {

        if (!file_exists($path)) {
            throw new Exception(".env file not found");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            $line = trim($line);

            // Ignorar comentarios
            if (strpos($line, '#') === 0) {
                continue;
            }

            // Separar clave y valor
            list($name, $value) = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }

    // Devuelve una variable ya cargada o un valor por defecto.
    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
