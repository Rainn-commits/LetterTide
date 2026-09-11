<?php

use Illuminate\Support\Facades\Storage;

function buildLogoDataUri(?string $logoPath): ?string
    {
        if (!$logoPath || !Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        try {
            $fileContent = Storage::disk('public')->get($logoPath);
            if (empty($fileContent)) {
                return null;
            }

            $mimeType = Storage::mimeType('public/' . $logoPath) ?? 'image/png';
            return 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        } catch (\Throwable $e) {
            return null;
        }
    }