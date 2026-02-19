const daftarDinasPPU = [
    { nama: "Dinas Pendidikan, Pemuda dan Olahraga", fokus: "Urusan sekolah, beasiswa, olahraga, dan kepemudaan." },
    { nama: "Dinas Kesehatan", fokus: "Layanan Puskesmas, RSUD, izin praktik tenaga medis, dan kesehatan masyarakat." },
    { nama: "Dinas Pekerjaan Umum dan Penataan Ruang", fokus: "Pembangunan jalan, jembatan, dan tata ruang wilayah." },
    { nama: "Dinas Kependudukan dan Catatan Sipil", fokus: "KTP-el, Kartu Keluarga, Akta Kelahiran, dan KIA." },
    { nama: "Dinas Komunikasi dan Informatika", fokus: "Informasi publik, infrastruktur IT, dan statistik daerah." },
    // ... Tambahkan dinas lainnya sesuai list yang kamu berikan
];
// Contoh tambahan dinas
const tambahanDinas = 
    [
  {
    "id": 1,
    "keluhan": "Lampu jalan di area Jembatan Pulau Balang mati saat malam hari.",
    "solusi": "Pengecekan jaringan kabel dan penggantian bohlam LED yang rusak.",
    "dinas": "Dinas Perhubungan (Dishub) PPU",
    "link": "https://dishub.penajamkab.go.id/"
  },
  {
    "id": 2,
    "keluhan": "Tumpukan sampah di sekitar Pasar Induk Penajam yang mulai berbau.",
    "solusi": "Penyediaan tambahan kontainer sampah dan penjadwalan rutin pengangkutan armada.",
    "dinas": "Dinas Lingkungan Hidup (DLH) PPU",
    "link": "https://dlh.penajamkab.go.id/"
  },
  {
    "id": 3,
    "keluhan": "Pipa distribusi air bersih mengalami kebocoran di Kelurahan Nipah-Nipah.",
    "solusi": "Perbaikan pipa utama dan normalisasi tekanan air ke rumah warga.",
    "dinas": "Perumda Air Minum Danum Taka",
    "link": "https://perumda-danumtaka.ppu.go.id/"
  },
  {
    "id": 4,
    "keluhan": "Jalanan berlubang di poros Maridan yang membahayakan pengendara motor.",
    "solusi": "Penambalan sementara menggunakan aspal cold mix sebelum pengaspalan permanen.",
    "dinas": "Dinas PUPR PPU",
    "link": "https://pupr.penajamkab.go.id/"
  },
  {
    "id": 5,
    "keluhan": "Gangguan koneksi internet di area perkantoran pemerintah kabupaten.",
    "solusi": "Optimalisasi bandwidth dan pemeliharaan rutin infrastruktur fiber optik.",
    "dinas": "Dinas Kominfo PPU",
    "link": "https://kominfo.penajamkab.go.id/"
  }
]
async function kirimPertanyaan(pesanUser) {
    // Gabungkan daftar dinas menjadi satu string konteks untuk prompting
    // const konteksDinas = daftarDinasPPU.map(d => `- ${d.nama}: ${d.fokus}`).join('\n');
    const konteksData = tambahanDinas.map(d => 
        `Dinas: ${d.dinas}
         Masalah Terkait: ${d.keluhan}
         Solusi Teknis: ${d.solusi}
         Website: ${d.link}`
    ).join('\n---\n');

    // 2. Buat Prompt yang lebih instruktif
    const promptUtama = `
        Anda adalah "PPU Smart Assistant", asisten AI resmi Kabupaten Penajam Paser Utara.
        
        TUGAS ANDA:
        1. Analisis pesan user: "${pesanUser}".
        2. Cari kecocokan masalah tersebut dengan data referensi di bawah ini:
        
        DATA REFERENSI:
        ${konteksData}
        
        ATURAN JAWABAN:
        - Jika ditemukan kecocokan (misal: user tanya soal jalan, air, atau sampah), berikan jawaban yang menyebutkan:
            a. Dinas yang bertanggung jawab.
            b. Solusi teknis yang akan/sedang dilakukan.
            c. Link website dinas terkait.
        - Gunakan nada bicara yang sopan, profesional, dan solutif.
        - Jika masalah user TIDAK ADA dalam referensi, arahkan mereka untuk menghubungi portal umum Pemkab PPU namun tetap berikan saran yang masuk akal.
        - Jawab langsung ke inti masalah dengan format yang elegan.
    `;

    // const promptUtama = `
    //     Anda adalah asisten AI resmi Kabupaten Penajam Paser Utara.
    //     Berikut adalah daftar dinas yang tersedia beserta fungsinya:
    //     ${konteksDinas}

    //     Jika pertanyaan user berkaitan dengan salah satu dinas di atas, berikan jawaban yang spesifik.
    //     Pertanyaan User: "${pesanUser}"
    // `;

    // Kirim prompt ini ke Route Laravel kamu
    const response = await axios.post('/ai/tanya', {
        prompt: promptUtama
    });

    console.log(response.data.jawaban);
}