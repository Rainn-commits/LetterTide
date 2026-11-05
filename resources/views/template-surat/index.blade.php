@extends('layouts.app')

@section('page-title', 'Kelola Template Surat')

@section('page-css')
    @vite('resources/css/template-surat.css')
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px 16px; background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 12px 16px; background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

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
                    <th class="is-sortable">Nama Template <span class="sort-icon">&#8645;</span></th>
                    <th class="col-versi is-sortable">Versi <span class="sort-icon">&#8645;</span></th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td class="template-name">{{ $template->nama_template }}</td>
                        <td class="col-versi">v{{ $template->versi }}</td>
                        <td>
                            @if ($template->is_aktif)
                                <span class="status-pill status-pill--active">Aktif</span>
                            @else
                                <span class="status-pill status-pill--inactive" style="background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; white-space: nowrap;">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('template-surat.preview', $template->id) }}" class="icon-btn icon-btn--ghost" title="Lihat preview" aria-label="Lihat preview">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>

                                @if ($template->is_aktif)
                                    <a href="{{ route('template-surat.edit', $template->id) }}" class="icon-btn" title="Edit template">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('template-surat.destroy', $template->id) }}" style="display:inline" data-confirm="Hapus template ini? Data yang dihapus tidak dapat dikembalikan." data-confirm-variant="danger" data-confirm-label="Ya, Hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn--ghost icon-btn--lg" title="Hapus template" aria-label="Hapus template" style="border:none; background:transparent; cursor:pointer;">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="table-empty">
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
