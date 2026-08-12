<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use App\Services\PlaceholderParser;

class SuratController extends Controller
{
    public function create()
    {
        $templates = TemplateSurat::where('is_aktif', true)
            ->orderBy('nama_template')
            ->get();

        return view('surat.pilih-template', compact('templates'));
    }

    public function form(TemplateSurat $template)
    {
        $fields = PlaceholderParser::toFormFields($template->isi_template);

        return view('surat.form', compact('template', 'fields'));
    }
}