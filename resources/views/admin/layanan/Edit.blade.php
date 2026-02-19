@extends('admin.layouts.app')

@section('title', 'Edit Layanan')
@section('breadcrumb', 'Edit Layanan Publik')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Edit Layanan Publik</div>
        <div class="page-subtitle">Perbarui data keluhan & solusi — #{{ $layanan->nomor ?? $layanan->id }}</div>
    </div>
    <a href="{{ route('admin.layanan.index') }}" class="btn btn-ghost">← Kembali</a>
</div>

<div style="max-width:760px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.layanan.update', $layanan) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Keluhan / Pertanyaan Masyarakat *</label>
                <textarea name="keluhan" class="form-control" rows="4" required>{{ old('keluhan', $layanan->keluhan) }}</textarea>
                @error('keluhan')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Solusi / Jawaban *</label>
                <textarea name="solusi" class="form-control" rows="6" required>{{ old('solusi', $layanan->solusi) }}</textarea>
                @error('solusi')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dinas Terkait</label>
                    <input type="text" name="dinas" class="form-control"
                           value="{{ old('dinas', $layanan->dinas) }}"
                           placeholder="Contoh: Dinas PUPR">
                </div>

                <div class="form-group">
                    <label class="form-label">Instagram Dinas</label>
                    <input type="text" name="instagram" class="form-control"
                           value="{{ old('instagram', $layanan->instagram) }}"
                           placeholder="@nama_akun">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Link Website Dinas</label>
                <input type="url" name="link" class="form-control"
                       value="{{ old('link', $layanan->link) }}"
                       placeholder="https://dinas.ppukab.go.id">
                @error('link')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status Validasi</label>
                    <select name="validation_status" class="form-control">
                        @foreach(['pending' => 'Pending', 'valid' => 'Valid', 'revisi' => 'Revisi', 'salah_mapping' => 'Salah Mapping'] as $val => $label)
                            <option value="{{ $val }}" {{ $layanan->validation_status == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Validasi</label>
                <textarea name="validation_note" class="form-control" rows="3"
                          placeholder="Catatan admin tentang status validasi...">{{ old('validation_note', $layanan->validation_note) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">✓ Simpan Perubahan</button>
                <a href="{{ route('admin.layanan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <!-- Metadata -->
    <div style="margin-top:16px; padding:14px 18px; background:var(--bg3); border-radius:var(--radius-sm); border:1px solid var(--border);">
        <div style="font-size:12px; color:var(--text3); display:flex; gap:20px; flex-wrap:wrap;">
            <span>ID: <strong style="color:var(--text2)">{{ $layanan->id }}</strong></span>
            <span>Nomor: <strong style="color:var(--text2)">{{ $layanan->nomor ?? '—' }}</strong></span>
            <span>Dibuat: <strong style="color:var(--text2)">{{ $layanan->created_at->format('d M Y, H:i') }}</strong></span>
            <span>Diperbarui: <strong style="color:var(--text2)">{{ $layanan->updated_at->format('d M Y, H:i') }}</strong></span>
        </div>
    </div>
</div>
@endsection