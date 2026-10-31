<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\CorrectiveAction;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Unit Kerja Pusri
        $units = [];
        $unitNames = [
            'Pabrik Pusri-IIB', 'Pabrik Pusri-III', 'Pabrik Pusri-IV',
            'Utilitas', 'Pengantongan', 'Pemeliharaan / Bengkel',
            'Laboratorium', 'Gudang', 'Perkantoran'
        ];
        foreach ($unitNames as $n) {
            $units[$n] = Unit::firstOrCreate(['nama' => $n]);
        }

        // 2. Master Jenis Insiden K3
        $types = [];
        $typeNames = [
            'Near Miss', 'First Aid', 'Kecelakaan Kerja',
            'Kebakaran / Ledakan', 'Kerusakan Aset', 'Pencemaran Lingkungan'
        ];
        foreach ($typeNames as $n) {
            $types[$n] = IncidentType::firstOrCreate(['nama' => $n]);
        }

        // 3. Akun Pengguna (Admin, Petugas K3, Pelapor)
        $admin = User::firstOrCreate(['email' => 'admin@k3.test'], [
            'name'     => 'Administrator K3',
            'nip'      => '198005122005011001',
            'role'     => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $petugasK3 = User::firstOrCreate(['email' => 'k3@k3.test'], [
            'name'     => 'Hendra Wijaya, ST (Petugas K3)',
            'nip'      => '198708212010121002',
            'role'     => 'petugas_k3',
            'password' => Hash::make('password123'),
        ]);

        $pelapor1 = User::firstOrCreate(['email' => 'pelapor@k3.test'], [
            'name'     => 'Rian Pratama',
            'nip'      => '199304152018031003',
            'role'     => 'pelapor',
            'unit_id'  => $units['Pabrik Pusri-IIB']->id,
            'password' => Hash::make('password123'),
        ]);

        $pelapor2 = User::firstOrCreate(['email' => 'ahmad@k3.test'], [
            'name'     => 'Ahmad Fauzi',
            'nip'      => '199102102016021004',
            'role'     => 'pelapor',
            'unit_id'  => $units['Pabrik Pusri-III']->id,
            'password' => Hash::make('password123'),
        ]);

        $pelapor3 = User::firstOrCreate(['email' => 'budi@k3.test'], [
            'name'     => 'Budi Santoso',
            'nip'      => '198911252014011005',
            'role'     => 'pelapor',
            'unit_id'  => $units['Pemeliharaan / Bengkel']->id,
            'password' => Hash::make('password123'),
        ]);

        // 4. Data Pelaporan Insiden Beragam Tanggal (Mei - Oktober 2026)
        $incidentsData = [
            // Mei 2026
            [
                'unit' => 'Pabrik Pusri-IIB',
                'type' => 'Near Miss',
                'reporter' => $pelapor1,
                'tanggal' => '2026-05-14 09:30:00',
                'lokasi' => 'Area Kompresor Gas Sintesis 101-J Lantai 2',
                'kronologi' => 'Saat inspeksi getaran rutin, ditemukan baut penahan cover pipa steam kendur dan bergetar hebat. Berpotensi lepas dan membahayakan operator yang melintas.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Vibrasi mekanis mesin kompresor yang melonggarkan baut pengikat.',
                'penyebab_dasar' => 'Jadwal pengecekan torsi baut pipa belum terdaftar pada checklist inspeksi mingguan.',
                'hasil_investigasi' => 'Tim mekanik langsung melakukan penghentian getaran dan pengencangan baut sesuai standar torsi 45 Nm.',
                'catatan_verifikasi' => 'Terverifikasi langsung di lapangan. Potensi bahaya mekanis telah diisolasi.',
                'actions' => [
                    ['Pengencangan kembali seluruh baut bracket cover pipa steam', 'Dedi (Supervisor Mekanik)', '2026-05-15', 'selesai'],
                    ['Penambahan pengecekan torsi baut pada checklist inspeksi preventif', 'Hendra Wijaya (K3)', '2026-05-20', 'selesai']
                ]
            ],
            [
                'unit' => 'Utilitas',
                'type' => 'Kerusakan Aset',
                'reporter' => $pelapor1,
                'tanggal' => '2026-05-27 14:15:00',
                'lokasi' => 'Rumah Pompa Air Pendingin Cooling Tower',
                'kronologi' => 'Kabel power motor pompa pendingin sekunder terkelupas pada bagian insulasi luar akibat gesekan terus-menerus dengan tepi tajam rangka penopang.',
                'status' => 'selesai',
                'severity' => 'ringan',
                'penyebab_langsung' => 'Gesekan mekanis antara kabel dan rangka besi tanpa pelindung pelindung pipa flexible.',
                'penyebab_dasar' => 'Kualitas instalasi jalur kabel kabel tray tidak dilengkapi grommet pelindung.',
                'hasil_investigasi' => 'Kabel sudah dimatikan arusnya sementara dan diganti insulasi baru dengan proteksi pipa flexible conduit.',
                'catatan_verifikasi' => 'Kondisi telah aman, tidak ada kebocoran arus ke bodi pompa.',
                'actions' => [
                    ['Pemasangan pelindung pipa flexible conduit dan isolasi tahan panas', 'Teknisi Listrik Utilitas', '2026-05-28', 'selesai']
                ]
            ],

            // Juni 2026
            [
                'unit' => 'Laboratorium',
                'type' => 'Pencemaran Lingkungan',
                'reporter' => $pelapor2,
                'tanggal' => '2026-06-11 10:45:00',
                'lokasi' => 'Meja Preparasi Kimia Basah Lab Pengendalian Mutu',
                'kronologi' => 'Tumpahan larutan reagen asam sulfat 250ml di atas meja preparasi saat pemindahan botol kaca. Uap menyengat sempat tercium namun segera dinetralisir.',
                'status' => 'selesai',
                'severity' => 'ringan',
                'penyebab_langsung' => 'Botol reagen licin saat dipegang menggunakan sarung tangan basah.',
                'penyebab_dasar' => 'Kondisi botol penyimpanan reagen tanpa grip karet anti-slip.',
                'hasil_investigasi' => 'Tumpahan dinetralisir dengan serbuk sodium bikarbonat dari chemical spill kit. Tidak ada cairan yang masuk ke saluran pembuangan air biasa.',
                'catatan_verifikasi' => 'Prosedur pembersihan spill kit dijalankan dengan baik oleh analis lab.',
                'actions' => [
                    ['Pembersihan tumpahan dan pembuangan limbah B3 sesuai prosedur', 'Analis Lab Kimia', '2026-06-11', 'selesai'],
                    ['Pemasangan karet silikon pelindung botol reagen kaca', 'Kepala Lab', '2026-06-15', 'selesai']
                ]
            ],
            [
                'unit' => 'Pemeliharaan / Bengkel',
                'type' => 'First Aid',
                'reporter' => $pelapor3,
                'tanggal' => '2026-06-23 11:20:00',
                'lokasi' => 'Meja Kerja Fabrikasi Bengkel Bubut',
                'kronologi' => 'Jari telunjuk mekanik tergores serpihan plat besi tajam saat proses pemotongan plat dengan gerinda tangan.',
                'korban' => 'Dedi Irawan (Mekanik)',
                'saksi' => 'Agus Priyono',
                'status' => 'selesai',
                'severity' => 'ringan',
                'penyebab_langsung' => 'Penggunaan sarung tangan katun biasa yang tidak tahan sayatan (non-cut resistant).',
                'penyebab_dasar' => 'Ketidaktersediaan stok sarung tangan safety Level 5 anti-cut di rak bengkel.',
                'hasil_investigasi' => 'Luka gores kecil langsung dibersihkan dan diperban di poliklinik plant. Korban dapat kembali bekerja normal.',
                'catatan_verifikasi' => 'Penanganan P3K telah selesai tanpa rujukan ke rumah sakit.',
                'actions' => [
                    ['Pengadaan dan kewajiban penggunaan sarung tangan safety anti-cut Level 5 di area bengkel bubut', 'Logistik K3', '2026-06-25', 'selesai']
                ]
            ],

            // Juli 2026
            [
                'unit' => 'Pengantongan',
                'type' => 'Near Miss',
                'reporter' => $pelapor1,
                'tanggal' => '2026-07-05 13:30:00',
                'lokasi' => 'Jalur Loading Dermaga Truk Pupuk Urea',
                'kronologi' => 'Palet berisi 40 karung pupuk urea miring dan nyaris roboh saat diangkat forklift melewati gundukan jalan penghubung dermaga loading.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Kecepatan forklift melebihi 10 km/jam saat melintasi polisi tidur jalur dermaga.',
                'penyebab_dasar' => 'Ketiadaan rambu batas kecepatan spesifik di jalur loading dermaga truk.',
                'hasil_investigasi' => 'Muatan berhasil diturunkan dengan aman menggunakan forklift cadangan tanpa ada karung yang pecah.',
                'catatan_verifikasi' => 'Safety briefing telah diberikan kepada seluruh operator forklift shift siang.',
                'actions' => [
                    ['Pemasangan rambu batas kecepatan forklift 5 km/jam dan perataan sambungan lantai dermaga', 'Seksi Sipil & Fasilitas', '2026-07-10', 'selesai']
                ]
            ],
            [
                'unit' => 'Gudang',
                'type' => 'Kerusakan Aset',
                'reporter' => $pelapor2,
                'tanggal' => '2026-07-20 16:10:00',
                'lokasi' => 'Gudang Material Suku Cadang Sektor Timur',
                'kronologi' => 'Saklar utama panel penerangan gudang timur mengeluarkan percikan api kecil dan bau hangus akibat penumpukan debu partikel halus.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Korsleting internal pada terminal kontak breaker akibat debu konduktif.',
                'penyebab_dasar' => 'Seal karet pintu panel distribusi listrik sudah getas sehingga debu masuk.',
                'hasil_investigasi' => 'Panel dimatikan dari breaker induk, debu disedot vacuum cleaner khusus, dan breaker MCB diganti baru.',
                'catatan_verifikasi' => 'Instalasi telah diuji mega-ohm tester dan dinyatakan layak operasional.',
                'actions' => [
                    ['Penggantian seal karet panel listrik dan pembersihan menyeluruh panel gudang', 'Listrik Gudang', '2026-07-22', 'selesai']
                ]
            ],

            // Agustus 2026
            [
                'unit' => 'Pabrik Pusri-III',
                'type' => 'Near Miss',
                'reporter' => $pelapor2,
                'tanggal' => '2026-08-07 08:45:00',
                'lokasi' => 'Jalur Pipa Kondensat Seksi Sintesis Lantai 1',
                'kronologi' => 'Pipa saluran buang kondensat steam bocor dan menyemburkan uap panas tepat di dekat tangga akses inspeksi harian.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Gasket sambungan flange pipa mengalami degradasi termal.',
                'penyebab_dasar' => 'Gasket melampaui masa pakai operasional (life cycle).',
                'hasil_investigasi' => 'Jalur diisolasi dengan tag out, gasket diganti tipe spiral wound tahan suhu tinggi, dan dipasang insulation jaket.',
                'catatan_verifikasi' => 'Kebocoran steam telah teratasi sepenuhnya, area tangga aman dilalui.',
                'actions' => [
                    ['Penggantian gasket flange pipa kondensat steam', 'Mekanik Pusri-III', '2026-08-08', 'selesai']
                ]
            ],
            [
                'unit' => 'Pemeliharaan / Bengkel',
                'type' => 'Kecelakaan Kerja',
                'reporter' => $pelapor3,
                'tanggal' => '2026-08-25 15:00:00',
                'lokasi' => 'Area Pencucian Mesin & Pompa Bengkel Tengah',
                'kronologi' => 'Teknisi terpeleset di lantai yang licin berminyak saat memindahkan pompa sentrifugal kecil, menyebabkan pergelangan kaki kanan terkilir ringan.',
                'korban' => 'Joko Susilo (Teknisi)',
                'saksi' => 'Taufik Hidayat',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Ceceran minyak pelumas mesin di lantai beton yang belum sempat dibersihkan sehabis disassembly mesin.',
                'penyebab_dasar' => 'Ketiadaan bak penampung tetesan oli (drip pan) saat pembongkaran pompa berminyak.',
                'hasil_investigasi' => 'Korban diperiksa di poliklinik, diberikan fisioterapi dan istirahat dokter selama 2 hari. Area lantai dicuci bersih dengan cairan degreaser dan dipasangi gritted safety mat.',
                'catatan_verifikasi' => 'Investigasi selesai. Dilakukan briefing 5R (Ringkas, Rapi, Resik, Rawat, Rajin) di bengkel.',
                'actions' => [
                    ['Pengadaan drip tray portable untuk penampung oli saat pembongkaran mesin', 'Kepala Bengkel', '2026-08-27', 'selesai'],
                    ['Pemasangan karpet karet anti-slip di area cuci pompa', 'Logistik K3', '2026-08-29', 'selesai']
                ]
            ],

            // September 2026
            [
                'unit' => 'Pabrik Pusri-IV',
                'type' => 'Near Miss',
                'reporter' => $pelapor1,
                'tanggal' => '2026-09-04 10:15:00',
                'lokasi' => 'Struktur Reaktor Urea Lantai 3',
                'kronologi' => 'Perancah (scaffolding) kerja inspeksi reaktor ditemukan kekurangan satu buah klem pengunci silang (cross brace clamp) pada tiang utama.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Pemasangan terburu-buru sebelum izin kerja ditandatangani pengawas.',
                'penyebab_dasar' => 'Belum dilakukannya inspeksi formal scaffolding tagging hijau oleh scaffolder inspector berlisensi.',
                'hasil_investigasi' => 'Pekerjaan dihentikan segera (Stop Work Authority). Klem silang dipasang lengkap dan scaffolding dinyatakan lolos uji beban sebelum diberi tag hijau.',
                'catatan_verifikasi' => 'Scaffolding telah diverifikasi aman dan berizin resmi.',
                'actions' => [
                    ['Pemasangan klem tambahan dan sertifikasi tag hijau scaffolding', 'Inspektur Perancah K3', '2026-09-04', 'selesai']
                ]
            ],
            [
                'unit' => 'Gudang',
                'type' => 'First Aid',
                'reporter' => $pelapor2,
                'tanggal' => '2026-09-19 14:30:00',
                'lokasi' => 'Gudang Palet & Kemasan Karung',
                'kronologi' => 'Pekerja logistik mengalami kram otot punggung ringan saat membantu mengangkat bundel karung kemasan secara manual.',
                'korban' => 'Rudi Hartono (Staf Gudang)',
                'saksi' => 'M. Yusuf',
                'status' => 'selesai',
                'severity' => 'ringan',
                'penyebab_langsung' => 'Posisi membungkuk saat mengangkat beban lebih dari 20 kg tanpa menekuk lutut.',
                'penyebab_dasar' => 'Kurangnya pemahaman teknik ergonomi pengangkatan beban manual (manual handling).',
                'hasil_investigasi' => 'Korban diberikan penanganan kompres hangat dan peregangan di poliklinik plant.',
                'catatan_verifikasi' => 'Edukasi ergonomi pengangkatan beban telah diberikan kepada regu gudang.',
                'actions' => [
                    ['Pelaksanaan safety talk mingguan tentang ergonomi angkat-angkut barang', 'Koordinator K3 Gudang', '2026-09-22', 'selesai']
                ]
            ],

            // Oktober 2026 (Bulan Ini)
            [
                'unit' => 'Utilitas',
                'type' => 'Near Miss',
                'reporter' => $pelapor1,
                'tanggal' => '2026-10-12 09:00:00',
                'lokasi' => 'Stasiun Distribusi Jalur Amonia Menuju Pusri-IIB',
                'kronologi' => 'Tercium bau gas amonia samar di sekitar valve sambungan flange pipa sirkulasi dingin. Alarm detektor portabel berbunyi pada angka 15 ppm.',
                'status' => 'selesai',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Peregangan baut sambungan flange akibat siklus ekspansi-kontraksi termal dingin.',
                'penyebab_dasar' => 'Flange belum diberi pelindung flange guard spray-shield.',
                'hasil_investigasi' => 'Petugas mengenakan masker respirator canister gas amonia dan mengencangkan flange baut. Konsentrasi gas kembali 0 ppm.',
                'catatan_verifikasi' => 'Pengukuran detektor gas pasca-tindakan menunjukkan kondisi normal dan aman.',
                'actions' => [
                    ['Pemasangan flange guard shield pelindung semprotan amonia', 'Mekanik Utilitas', '2026-10-13', 'selesai']
                ]
            ],
            [
                'unit' => 'Pabrik Pusri-IIB',
                'type' => 'Kerusakan Aset',
                'reporter' => $pelapor1,
                'tanggal' => '2026-10-24 11:20:00',
                'lokasi' => 'Lantai 1 Pompa Sirkulasi Air Pendingin CW-102',
                'kronologi' => 'Sensor suhu bearing motor pompa sirkulasi air menunjukkan pembacaan naik mencapai 78°C (batas normal 70°C). Terdapat indikasi kekurangan pelumas grease.',
                'status' => 'tindak_lanjut',
                'severity' => 'sedang',
                'penyebab_langsung' => 'Penurunan viskositas minyak pelumas bearing pompa akibat operasi nonstop.',
                'penyebab_dasar' => 'Jadwal re-greasing berkala terlewat saat siklus shutdown parsial pabrik.',
                'hasil_investigasi' => 'Tim mekanik menjadwalkan pergantian pelumas dan pengetesan vibrasi motor pada shift malam.',
                'catatan_verifikasi' => 'Sedang dalam pengawasan ketat, temperatur dimonitor berkala setiap 2 jam.',
                'actions' => [
                    ['Penggantian pelumas grease bearing dan flushing oli lama', 'Tim Pemeliharaan Pabrik IIB', '2026-11-02', 'proses'],
                    ['Kalibrasi ulang sensor thermocouple temperatur bearing', 'Instrumen Pusri-IIB', '2026-11-04', 'belum']
                ]
            ],
            [
                'unit' => 'Pengantongan',
                'type' => 'Near Miss',
                'reporter' => $pelapor2,
                'tanggal' => '2026-10-29 13:45:00',
                'lokasi' => 'Konveyor Line 3 Pengemasan Pupuk Urea Prill',
                'kronologi' => 'Konveyor ban pengangkut karung pupuk tersangkut potongan tali rafia dan serabut karung pada roller pemutar, menimbulkan suara gesekan keras.',
                'status' => 'investigasi',
                'severity' => 'ringan',
                'penyebab_langsung' => 'Sisa potongan benang karung menumpuk di sela bearing roller konveyor.',
                'penyebab_dasar' => 'Pembersihan harian rol konveyor belum maksimal sebelum pergantian shift.',
                'hasil_investigasi' => 'Roller dibersihkan, saat ini sedang diobservasi oleh pengawas seksi pengantongan.',
                'catatan_verifikasi' => 'Laporan diverifikasi, tidak ada kerusakan fisik pada belt konveyor.',
                'actions' => [
                    ['Pembersihan tuntas seluruh roller konveyor Line 1 s/d Line 4', 'Pengawas Shift Pengantongan', '2026-11-01', 'belum']
                ]
            ],
            [
                'unit' => 'Pabrik Pusri-IV',
                'type' => 'Near Miss',
                'reporter' => $pelapor1,
                'tanggal' => '2026-10-31 08:30:00',
                'lokasi' => 'Bordes Tangga Akses Lantai 2 Unit Amonia',
                'kronologi' => 'Ditemukan ceceran oli pelumas di bordes tangga baja lantai 2 tanpa ada papan peringatan lantai licin. Nyaris menyebabkan operator tergelincir saat patroli pagi.',
                'status' => 'dilaporkan',
                'severity' => null,
                'penyebab_langsung' => null,
                'penyebab_dasar' => null,
                'hasil_investigasi' => null,
                'catatan_verifikasi' => null,
                'actions' => []
            ],
        ];

        // Buat record incident dan action
        foreach ($incidentsData as $idx => $d) {
            $tgl = Carbon::parse($d['tanggal']);
            $incident = Incident::create([
                'kode' => 'INS-' . $tgl->format('Ymd') . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'reporter_id' => $d['reporter']->id,
                'incident_type_id' => $types[$d['type']]->id,
                'unit_id' => $units[$d['unit']]->id,
                'tanggal_kejadian' => $tgl,
                'lokasi_detail' => $d['lokasi'],
                'kronologi' => $d['kronologi'],
                'korban' => $d['korban'] ?? null,
                'saksi' => $d['saksi'] ?? null,
                'status' => $d['status'],
                'severity' => $d['severity'],
                'catatan_verifikasi' => $d['catatan_verifikasi'] ?? null,
                'verified_by' => $d['status'] !== 'dilaporkan' ? $petugasK3->id : null,
                'verified_at' => $d['status'] !== 'dilaporkan' ? (clone $tgl)->addHours(2) : null,
                'penyebab_langsung' => $d['penyebab_langsung'] ?? null,
                'penyebab_dasar' => $d['penyebab_dasar'] ?? null,
                'hasil_investigasi' => $d['hasil_investigasi'] ?? null,
                'closed_at' => $d['status'] === 'selesai' ? (clone $tgl)->addDays(2) : null,
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            // Corrective Actions
            foreach ($d['actions'] as [$deskripsi, $pj, $tenggat, $statusAksi]) {
                CorrectiveAction::create([
                    'incident_id' => $incident->id,
                    'deskripsi' => $deskripsi,
                    'penanggung_jawab' => $pj,
                    'tenggat' => Carbon::parse($tenggat),
                    'status' => $statusAksi,
                    'created_at' => $tgl,
                    'updated_at' => $tgl,
                ]);
            }

            // Catat audit log
            AuditLog::create([
                'user_id' => $d['reporter']->id,
                'aksi' => 'buat_laporan',
                'deskripsi' => "Membuat laporan {$incident->kode}",
                'ip' => '127.0.0.1',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            if ($d['status'] !== 'dilaporkan') {
                AuditLog::create([
                    'user_id' => $petugasK3->id,
                    'aksi' => 'verifikasi',
                    'deskripsi' => "Memverifikasi {$incident->kode} (Keparahan: {$d['severity']})",
                    'ip' => '127.0.0.1',
                    'created_at' => (clone $tgl)->addHours(2),
                    'updated_at' => (clone $tgl)->addHours(2),
                ]);
            }
        }
    }
}
