@extends('layouts.app')

@section('page-title', 'Arsip Surat')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    <div class="wizard-step">
        <h2 class="wizard-step__title">Arsip Surat</h2>
    </div>

    <form method="GET" action="{{ route('arsip.index') }}" class="card" style="margin-bottom: 20px; padding: 15px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px;">
            
            @if (Auth::user()->hasAnyRole(['Super Admin', 'Admin']))
                <div>
                    <label style="font-size: 12px; font-weight: 600;">Cakupan Surat</label>
                    <select name="pembuat" class="form-control" style="width:100%; padding: 6px 10px;">
                        <option value="semua" {{ request('pembuat') === 'semua' ? 'selected' : '' }}>Semua Surat</option>
                        <option value="saya" {{ request('pembuat') === 'saya' ? 'selected' : '' }}>Surat Buatan Saya</option>
                    </select>
                </div>
            @endif

            <div>
                <label style="font-size: 12px; font-weight: 600;">Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor/Template/Pembuat..." class="form-control" style="width:100%; padding: 6px 10px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 600;">Jenis Surat</label>
                <select name="jenis" class="form-control" style="width:100%; padding: 6px 10px;">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Keluar" {{ request('jenis') === 'Keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="Masuk" {{ request('jenis') === 'Masuk' ? 'selected' : '' }}>Masuk</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 600;">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control" style="width:100%; padding: 6px 10px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 600;">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control" style="width:100%; padding: 6px 10px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 600;">Status File</label>
                <select name="status_arsip" class="form-control" style="width:100%; padding: 6px 10px;">
                    <option value="">-- Semua --</option>
                    <option value="uploaded" {{ request('status_arsip') === 'uploaded' ? 'selected' : '' }}>Ada File</option>
                    <option value="pending" {{ request('status_arsip') === 'pending' ? 'selected' : '' }}>Belum Upload</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 15px; text-align: right;">
            <a href="{{ route('arsip.index') }}" class="btn-ghost" style="margin-right: 8px;">Reset Filter</a>
            <button type="submit" class="btn-dark">Cari & Filter</button>
        </div>
    </form>

    <div class="card card--table">
        <table class="template-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Nomor Surat</th>
                    <th>Template</th>
                    <th>Pembuat</th>
                    <th>Tanggal</th>
                    <th>File Arsip</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratList as $surat)
                    <tr>
                        <td><strong>{{ $surat->nomor_surat }}</strong></td>
                        <td>{{ $surat->template->nama_template ?? 'Template Dihapus' }}</td>
                        <td>{{ $surat->user->name ?? '-' }}</td>
                        <td>{{ $surat->tanggal_surat->format('d/m/Y') }}</td>
                        <td>
                            @if ($surat->file_arsip)
                                <a href="{{ asset('storage/' . $surat->file_arsip) }}" target="_blank" style="color: #0f5132; font-weight: 600;">
                                    Lihat File
                                </a>
                            @else
                                <span style="color: #6c757d;">Belum ada</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('surat.show', $surat) }}" class="btn-ghost" style="padding: 4px 8px; font-size: 12px;">Detail</a>
                            <a href="{{ route('surat.cetak', $surat) }}" target="_blank" class="btn-ghost" style="padding: 4px 8px; font-size: 12px;">Cetak</a>

                            <form action="{{ route('surat.upload-arsip', $surat) }}" method="POST" enctype="multipart/form-data" style="display:inline-block; margin-left: 4px;">
                                @csrf
                                <input type="file" name="file_arsip" required style="display:none;" id="file-{{ $surat->id }}" onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('file-{{ $surat->id }}').click()" class="btn-dark" style="padding: 4px 8px; font-size: 12px;">
                                    {{ $surat->file_arsip ? 'Ganti File' : 'Upload File' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">
                            Tidak ada data arsip surat yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($suratList->hasPages())
        <div style="margin-top: 20px;">
            {{ $suratList->links() }}
        </div>
    @endif

@endsection