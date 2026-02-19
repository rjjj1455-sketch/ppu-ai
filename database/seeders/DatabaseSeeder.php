<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Chat;
use App\Models\LayananPublik;
use App\Models\User;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Users ──────────────────────────────────────────────
        User::create([
            'name'     => 'Super Admin PPU',
            'email'    => 'superadmin@ppu.go.id',
            'password' => Hash::make('password'),
            'role'     => 'superadmin',
        ]);

        User::create([
            'name'     => 'Admin Diskominfo',
            'email'    => 'admin@ppu.go.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Layanan Publik ───────────────────────────────────────────
        $layananData = [
            [
                'nomor'             => 1,
                'keluhan'           => 'Jalan rusak di Kecamatan Sepaku menuju area IKN Nusantara belum diperbaiki',
                'solusi'            => 'Dinas PUPR Kabupaten PPU telah menjadwalkan perbaikan jalan di Kecamatan Sepaku pada kuartal pertama 2025. Tim survei telah turun ke lapangan untuk melakukan penilaian kerusakan.',
                'dinas'             => 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)',
                'link'              => 'https://pupr.ppukab.go.id',
                'instagram'         => '@pupr_ppu',
                'validation_status' => 'valid',
            ],
            [
                'nomor'             => 2,
                'keluhan'           => 'Air PDAM tidak mengalir selama 3 hari di Kelurahan Penajam',
                'solusi'            => 'Gangguan distribusi air PDAM disebabkan perbaikan pipa induk. PERUMDA Tirta Benuo Taka PPU memastikan pelayanan normal dalam 2x24 jam.',
                'dinas'             => 'PERUMDA Tirta Benuo Taka',
                'link'              => 'https://pdam.ppukab.go.id',
                'instagram'         => '@pdamppu',
                'validation_status' => 'valid',
            ],
            [
                'nomor'             => 3,
                'keluhan'           => 'Tumpukan sampah di TPS Babulu tidak diangkut sudah seminggu',
                'solusi'            => 'Dinas Lingkungan Hidup PPU akan menambah armada pengangkutan sampah di Kecamatan Babulu. Jadwal pengangkutan ditingkatkan menjadi 3x seminggu.',
                'dinas'             => 'Dinas Lingkungan Hidup',
                'link'              => 'https://dlh.ppukab.go.id',
                'instagram'         => '@dlh_ppu',
                'validation_status' => 'valid',
            ],
            [
                'nomor'             => 4,
                'keluhan'           => 'Puskesmas Waru kekurangan tenaga dokter spesialis',
                'solusi'            => 'Dinas Kesehatan PPU sedang dalam proses rekrutmen dokter spesialis untuk Puskesmas Waru. Program dokter PTT juga sedang dikoordinasikan dengan Kemenkes.',
                'dinas'             => 'Dinas Kesehatan',
                'link'              => 'https://dinkes.ppukab.go.id',
                'instagram'         => '@dinaskesppu',
                'validation_status' => 'revisi',
                'validation_note'   => 'Perlu diperbarui dengan info jadwal rekrutmen terbaru',
            ],
            [
                'nomor'             => 5,
                'keluhan'           => 'Sinyal internet lemah di Desa Maridan Kecamatan Sepaku',
                'solusi'            => 'Diskominfo PPU berkoordinasi dengan provider untuk pemasangan BTS baru di Desa Maridan sebagai bagian dari program digitalisasi desa menuju IKN.',
                'dinas'             => 'Dinas Komunikasi dan Informatika',
                'link'              => 'https://diskominfo.ppukab.go.id',
                'instagram'         => '@diskominfoppu',
                'validation_status' => 'pending',
            ],
            [
                'nomor'             => 6,
                'keluhan'           => 'Lampu jalan mati di sepanjang Jalan Provinsi Nipah-Nipah',
                'solusi'            => 'Dinas Perhubungan PPU akan melakukan pengecekan dan penggantian lampu PJU yang rusak. Laporan diterima dan masuk antrian perbaikan.',
                'dinas'             => 'Dinas Perhubungan',
                'link'              => 'https://dishub.ppukab.go.id',
                'instagram'         => '@dishub_ppu',
                'validation_status' => 'valid',
            ],
            [
                'nomor'             => 7,
                'keluhan'           => 'Sekolah SDN 009 Penajam atapnya bocor saat hujan',
                'solusi'            => 'Dinas Pendidikan PPU mencatat laporan ini dan akan memasukkan SDN 009 Penajam dalam program rehabilitasi gedung sekolah tahun anggaran 2025.',
                'dinas'             => 'Dinas Pendidikan dan Kebudayaan',
                'link'              => 'https://dikbud.ppukab.go.id',
                'instagram'         => '@dinaspendppu',
                'validation_status' => 'pending',
            ],
            [
                'nomor'             => 8,
                'keluhan'           => 'Banjir rutin di Kelurahan Petung saat musim hujan',
                'solusi'            => 'Dinas PUPR PPU merencanakan pembangunan drainase di Kelurahan Petung. Studi kelayakan sedang dilaksanakan untuk menentukan solusi penanganan banjir yang permanen.',
                'dinas'             => 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)',
                'link'              => 'https://pupr.ppukab.go.id',
                'instagram'         => '@pupr_ppu',
                'validation_status' => 'salah_mapping',
                'validation_note'   => 'Seharusnya ditangani oleh BPBD, bukan PUPR',
            ],
            [
                'nomor'             => 9,
                'keluhan'           => 'Perizinan usaha di Kecamatan Babulu prosesnya terlalu lama',
                'solusi'            => 'DPMPTSP PPU menerapkan sistem OSS (Online Single Submission) yang dapat diakses secara daring. Masyarakat diminta mendaftarkan izin usaha melalui portal oss.go.id.',
                'dinas'             => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu (DPMPTSP)',
                'link'              => 'https://dpmptsp.ppukab.go.id',
                'instagram'         => '@dpmptsp_ppu',
                'validation_status' => 'valid',
            ],
            [
                'nomor'             => 10,
                'keluhan'           => 'Kekurangan pasokan listrik sering padam di Desa Mentawir',
                'solusi'            => 'Diskominfo PPU telah berkoordinasi dengan PLN UP3 Balikpapan untuk penanganan gangguan listrik di Desa Mentawir. PLN menjanjikan penanganan dalam 1 minggu.',
                'dinas'             => 'Dinas Komunikasi dan Informatika',
                'link'              => 'https://diskominfo.ppukab.go.id',
                'instagram'         => '@diskominfoppu',
                'validation_status' => 'revisi',
                'validation_note'   => 'Koordinasi lebih tepat ke ESDM atau langsung PLN',
            ],
        ];

        foreach ($layananData as $data) {
            LayananPublik::create($data);
        }

        // ── Chat History (simulasi 7 hari terakhir) ──────────────────
        $chatSamples = [
            ['user_message' => 'Bagaimana cara mengurus KTP di PPU?', 'ai_response' => 'Untuk mengurus KTP di Penajam Paser Utara, Anda bisa datang ke Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil) PPU di Jl. Propinsi No.1 Penajam. Bawa fotokopi KK dan surat pengantar dari RT/RW.'],
            ['user_message' => 'Halo, ada keluhan soal jalan rusak di Sepaku', 'ai_response' => 'Selamat datang! Untuk keluhan jalan rusak di Sepaku, silakan laporkan ke Dinas PUPR PPU melalui website pupr.ppukab.go.id atau Instagram @pupr_ppu.'],
            ['user_message' => 'Kapan IKN Nusantara mulai beroperasi penuh?', 'ai_response' => 'Berdasarkan informasi terkini, IKN Nusantara ditargetkan mulai beroperasi secara bertahap. Proses pembangunan infrastruktur dasar terus berlangsung di wilayah Sepaku, PPU.'],
            ['user_message' => 'Dinas apa yang menangani masalah air bersih?', 'ai_response' => 'Masalah air bersih di PPU ditangani oleh PERUMDA Tirta Benuo Taka. Anda bisa menghubungi mereka di pdam.ppukab.go.id untuk laporan gangguan air.'],
            ['user_message' => 'Titik nol IKN ada di mana tepatnya?', 'ai_response' => 'Titik nol IKN Nusantara terletak di Kecamatan Sepaku, Kabupaten Penajam Paser Utara (PPU), Kalimantan Timur. Lokasinya dapat diakses dari Balikpapan sekitar 40 km.'],
            ['user_message' => 'Sampah di TPS Babulu tidak diangkut', 'ai_response' => 'Terima kasih atas laporannya. Masalah sampah di Babulu ditangani oleh Dinas Lingkungan Hidup PPU. Silakan hubungi @dlh_ppu di Instagram untuk pelaporan cepat.'],
            ['user_message' => 'Bagaimana kondisi sinyal internet di daerah IKN?', 'ai_response' => 'Infrastruktur telekomunikasi di kawasan IKN terus ditingkatkan. Diskominfo PPU berkoordinasi dengan provider untuk memperluas jangkauan sinyal terutama di Desa Maridan dan sekitarnya.'],
            ['user_message' => 'Puskesmas terdekat dari Sepaku ada di mana?', 'ai_response' => 'Puskesmas terdekat di Kecamatan Sepaku adalah Puskesmas Sepaku. Untuk info jam operasional dan layanan, hubungi Dinas Kesehatan PPU di @dinaskesppu.'],
            ['user_message' => 'Halo pagi', 'ai_response' => 'Selamat pagi! Saya PPU AI, siap membantu Anda dengan informasi seputar Penajam Paser Utara dan IKN Nusantara. Ada yang bisa saya bantu?'],
            ['user_message' => 'Bagaimana cara daftar UMKM di PPU?', 'ai_response' => 'Pendaftaran UMKM di PPU dapat dilakukan melalui DPMPTSP PPU secara online maupun offline. Kunjungi dpmptsp.ppukab.go.id atau Instagram @dpmptsp_ppu untuk informasi lengkap.'],
            ['user_message' => 'Apakah ada bantuan sosial untuk warga PPU?', 'ai_response' => 'Ya, Dinas Sosial PPU mengelola berbagai program bantuan sosial seperti PKH dan BPNT. Silakan kunjungi kantor Dinas Sosial PPU atau website resmi Pemkab PPU untuk informasi lebih lanjut.'],
            ['user_message' => 'Lokasi kantor bupati PPU di mana?', 'ai_response' => 'Kantor Bupati Penajam Paser Utara berlokasi di Jl. Propinsi No.1, Penajam, Kabupaten Penajam Paser Utara, Kalimantan Timur 76211.'],
        ];

        // Sebar chat dalam 7 hari terakhir
        foreach ($chatSamples as $i => $chat) {
            $daysAgo = rand(0, 6);
            $hoursAgo = rand(0, 23);
            Chat::create(array_merge($chat, [
                'created_at' => Carbon::now()->subDays($daysAgo)->subHours($hoursAgo),
                'updated_at' => Carbon::now()->subDays($daysAgo)->subHours($hoursAgo),
            ]));
        }
    }
}