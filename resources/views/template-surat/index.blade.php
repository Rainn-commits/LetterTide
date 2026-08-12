@extends('layouts.app')

@section('page-title', 'Kelola Template Surat')

@section('page-css')
    @vite('resources/css/index.css')
@endsection

@section('content')

    <div class="page-toolbar">
        <div class="page-toolbar__spacer"></div>
        <a href="{{ route('template-surat.create') }}" class="btn-teal">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
            Tambah Template Baru
        </a>
    </div>

    <div class="card card--table">
        <table class="template-table">
            <thead>
                <tr>
                    <th class="col-checkbox">
                        <input type="checkbox" id="selectAll" aria-label="Pilih semua template">
                    </th>
                    <th class="is-sortable">Nama Template <span class="sort-icon">&#8645;</span></th>
                    <th class="col-versi is-sortable">Versi <span class="sort-icon">&#8645;</span></th>
                    <th class="col-status is-sortable">Status <span class="sort-icon">&#8645;</span></th>
                    <th class="col-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td>
                            <input type="checkbox" name="selected[]" value="{{ $template->id }}" aria-label="Pilih {{ $template->nama_template }}">
                        </td>
                        <td class="template-name">{{ $template->nama_template }}</td>
                        <td>v{{ $template->versi }}</td>
                        <td>
                            @if ($template->status === 'Aktif')
                                <span class="status-pill status-pill--active">Aktif</span>
                            @else
                                <span class="status-pill status-pill--inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('template-surat.edit', $template->id) }}" class="icon-btn" title="Edit template" aria-label="Edit template">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 20L4.6 16.2C4.7 15.5 5 14.9 5.5 14.4L15.4 4.5C16.2 3.7 17.5 3.7 18.3 4.5L19.5 5.7C20.3 6.5 20.3 7.8 19.5 8.6L9.6 18.5C9.1 19 8.5 19.3 7.8 19.4L4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <a href="{{ route('template-surat.history', $template->id) }}" class="icon-btn icon-btn--ghost" title="Riwayat versi" aria-label="Riwayat versi">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 12a9 9 0 109-9 9 9 0 00-6.7 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        <path d="M3 4v4h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="table-empty">
                            Belum ada template surat. Klik "Tambah Template Baru" untuk membuat yang pertama.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($templates ?? null, 'links'))
        <div style="margin-top: 20px;">
            {{ $templates->links() }}
        </div>
    @endif

@endsection
