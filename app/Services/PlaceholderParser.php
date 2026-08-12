<?php

namespace App\Services;

class PlaceholderParser
{
    public static function extract(string $text): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $text, $matches);

        return array_values(array_unique($matches[1]));
    }

    public static function toFormFields(string $text): array
    {
        $names = self::extract($text);

        return array_map(function ($name) {
            return [
                'name'  => $name,
                'label' => ucwords(str_replace('_', ' ', $name)),
                'type'  => self::inferType($name),
            ];
        }, $names);
    }

    private static function inferType(string $name): string
    {
        $lower = strtolower($name);

        if (str_contains($lower, 'tanggal')) {
            return 'date';
        }

        if (str_contains($lower, 'nama')) {
            return 'autocomplete_user';
        }

        return 'text';
    }
}