<?php

namespace App\Services;

class PlaceholderParser
{
    public static function extract(string $text): array
    {
        preg_match_all('/\{\{\s*(.*?)\s*\}\}|\[\s*(.*?)\s*\]|\{\s*(.*?)\s*\}/', $text, $matches);

        $rawFields = array_merge($matches[1], $matches[2], $matches[3]);

        $fields = array_filter(array_map('trim', $rawFields));

        return array_values(array_unique($fields));
    }

    public static function toFormFields(string $text): array
    {
        $names = self::extract($text);

        return array_map(function ($name) {
            return [
                'name'  => $name,
                'label' => ucwords(str_replace(['_', '-'], ' ', $name)),
                'type'  => self::inferType($name),
            ];
        }, $names);
    }

    public static function render(string $text, array $values): string
    {
        foreach ($values as $name => $value) {
            $search = [
                '{{ ' . $name . ' }}',
                '{{' . $name . '}}',
                '[' . $name . ']',
                '{' . $name . '}',
            ];

            $text = str_replace($search, $value, $text);
        }

        return preg_replace_callback(
            '/\{\{\s*(.*?)\s*\}\}|\[\s*(.*?)\s*\]|\{\s*(.*?)\s*\}/',
            function ($matches) use ($values) {
                $key = trim($matches[1] ?: ($matches[2] ?: $matches[3]));
                return $values[$key] ?? '';
            },
            $text
        );
    }

    private static function inferType(string $name): string
    {
        $lower = strtolower($name);

        if (str_contains($lower, 'tanggal')) {
            return 'date';
        }

        if (str_contains($lower, 'karyawan') || str_contains($lower, 'pegawai') || str_contains($lower, 'user')) {
            return 'autocomplete_user';
        }

        return 'text';
    }
}

