<?php

namespace App\Services;

class PlaceholderParser
{
    public static function extract(string $text): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $text, $matches);

        return array_values(array_unique($matches[1]));
    }
}