@if (auth()->user()->isGlobal())
    @php $daftarUnitPilihan = \App\Models\UnitKerja::orderBy('id')->get(); @endphp
    <div class="form-field" style="margin-bottom: 16px;">
        <label for="unit_kerja_id" style="display: block; font-weight: 600; margin-bottom: 6px; text-transform: none;">Unit Kerja:</label>
        <select name="unit_kerja_id" id="unit_kerja_id" required
                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; background: #fff;">
            <option value="">-- Pilih Unit Kerja --</option>
            @foreach ($daftarUnitPilihan as $unitPilihan)
                <option value="{{ $unitPilihan->id }}" {{ (string) ($nilai ?? '') === (string) $unitPilihan->id ? 'selected' : '' }}>
                    {{ $unitPilihan->nama }}
                </option>
            @endforeach
        </select>
        @isset($keterangan)
            <p style="font-size: 12.5px; color: #64748b; margin: 6px 0 0;">{{ $keterangan }}</p>
        @endisset
        @error('unit_kerja_id')
            <p style="color: #dc2626; font-size: 12.5px; margin: 6px 0 0;">{{ $message }}</p>
        @enderror
    </div>
@endif
