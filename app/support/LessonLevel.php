<?php

// Centraliza los niveles disponibles para las clases.

class LessonLevel
{
    public const INITIAL = 'Inicial';
    public const MEDIUM = 'Medio';
    public const ADVANCED = 'Avanzado';

    // Devuelve los niveles fijos para combos y validaciones.
    public static function all(): array
    {
        return [
            self::INITIAL,
            self::MEDIUM,
            self::ADVANCED
        ];
    }

    // Verifica que el nivel enviado exista dentro del sistema.
    public static function isValid(string $level): bool
    {
        return in_array($level, self::all(), true);
    }
}
