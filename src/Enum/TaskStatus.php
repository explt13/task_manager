<?php

namespace App\Enum;

enum TaskStatus: string
{
    case Active = "active";
    case Completed = "completed";
    public static function values(): array
    {
        return array_map(
            fn(TaskStatus $status) => $status->value,
            self::cases()
        );
    }
}