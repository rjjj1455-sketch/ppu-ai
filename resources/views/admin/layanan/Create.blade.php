@extends('admin.layouts.app')

@section('title', 'Tambah Layanan')
@section('breadcrumb', 'Tambah Layanan Publik')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Tambah Layanan Publik</div>
        <div class="page-subtitle">Input data keluhan dan solusi layanan masyarakat PPU baru</div>
    </div>
    <a href="{{ route('admin.layanan.index') }}" class="btn btn-ghost">← Kembali</a>
</div>

<div style="max-width: 760px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.layanan.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Keluhan / Pertanyaan Masyarakat *</label>
                <textarea name="keluhan" class="form-control" rows="4"
                          placeholder="Contoh: Jalan rusak di Kecamatan Sepaku belum diperbaiki..."
                          required>{{ old('keluhan') }}</textarea>
                @error('keluhan')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Solusi / Jawaban *</label>
                <textarea name="solusi" class="form-control" rows="6"
                          placeholder="Contoh: Dinas PUPR PPU akan melakukan perbaikan jalan..."
                          required>{{ old('solusi') }}</textarea>
                @error('solusi')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dinas Terkait</label>
                    <input type="text" name="dinas" class="form-control"
                           placeholder="Contoh: Dinas PUPR"
                           value="{{ old('dinas') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Instagram Dinas</label>
                    <input type="text" name="instagram" class="form-control"
                           placeholder="Contoh: @pupr_ppu"
                           value="{{ old('instagram') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Link Website Dinas</label>
                <input type="url" name="link" class="form-control"
                       placeholder="https://pupr.ppukab.go.id"
                       value="{{ old('link') }}">
                @error('link')
                    <div style="color:var(--red); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status Validasi</label>
                <select name="validation_status" class="form-control">
                    <option value="pending" selected>Pending</option>
                    <option value="valid">Valid</option>
                    <option value="revisi">Revisi</option>
                    <option value="salah_mapping">Salah Mapping</option>
                </select>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">+ Simpan Data</button>
                <a href="{{ route('admin.layanan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection