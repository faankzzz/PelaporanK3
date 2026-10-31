<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Insiden {{ $incident->kode }} - K3 PT Pupuk Sriwidjaja Palembang</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; margin: 30px; color: #1e293b; line-height: 1.5; }
        .kop { border-bottom: 3px double #004b87; padding-bottom: 12px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; }
        .kop h2 { margin: 0; color: #004b87; font-size: 20px; font-weight: 800; }
        .kop h3 { margin: 2px 0 0; color: #b28000; font-size: 14px; font-weight: 700; text-transform: uppercase; }
        .kop p { margin: 3px 0 0; font-size: 11px; color: #64748b; }
        .doc-title { text-align: center; margin: 15px 0; text-transform: uppercase; }
        .doc-title h4 { margin: 0; font-size: 16px; color: #00264d; text-decoration: underline; }
        .doc-title span { font-size: 12px; color: #475569; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        td, th { border: 1px solid #cbd5e1; padding: 7px 10px; vertical-align: top; text-align: left; }
        th { background: #f1f5f9; color: #0f172a; font-weight: 700; }
        td.k { width: 28%; background: #f8fafc; font-weight: 600; color: #334155; }
        .section-heading { background: #004b87; color: #ffffff; padding: 5px 10px; font-weight: bold; font-size: 12px; margin-bottom: 6px; border-radius: 3px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #e2e8f0; }
        .signature-box { display: flex; justify-content: space-between; margin-top: 35px; page-break-inside: avoid; }
        .sig-col { width: 40%; text-align: center; }
        .sig-space { height: 60px; }
        .noprint { margin-bottom: 20px; background: #fff9e6; border: 1px solid #ffb800; padding: 10px 15px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
        .btn-print { background: #004b87; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        @media print { .noprint { display: none; } body { margin: 10mm; } }
    </style>
</head>
<body>
<div class="noprint">
    <div><strong>Laporan Resmi Insiden K3</strong> &mdash; Siap dicetak atau disimpan sebagai dokumen PDF resmi.</div>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="kop">
    <div>
        <h2>PT PUPUK SRIWIDJAJA PALEMBANG</h2>
        <h3>Departemen Keselamatan &amp; Kesehatan Kerja (K3)</h3>
        <p>Jl. Mayor Zen, Palembang 30118, Sumatera Selatan &bull; Telepon Darurat K3: ext. 2222 / 0711-712222</p>
    </div>
    <div style="text-align: right; font-size: 11px; color: #475569;">
        <strong>K3 PUSRI SAFE</strong><br>
        Dokumen Formulir Investigasi
    </div>
</div>

<div class="doc-title">
    <h4>BERITA ACARA LAPORAN INSIDEN KERJA</h4>
    <span>Nomor Registrasi: <strong>{{ $incident->kode }}</strong></span>
</div>

<div class="section-heading">I. DATA INSIDEN &amp; PELAPORAN</div>
<table>
    <tr>
        <td class="k">Jenis Kejadian</td>
        <td><strong>{{ $incident->type->nama }}</strong></td>
    </tr>
    <tr>
        <td class="k">Unit / Area Pabrik</td>
        <td>{{ $incident->unit->nama }}</td>
    </tr>
    <tr>
        <td class="k">Lokasi Spesifik</td>
        <td>{{ $incident->lokasi_detail }}</td>
    </tr>
    <tr>
        <td class="k">Waktu Kejadian</td>
        <td>{{ $incident->tanggal_kejadian->format('d/m/Y H:i') }} WIB</td>
    </tr>
    <tr>
        <td class="k">Pelapor</td>
        <td>{{ $incident->reporter->name }} (NIP: {{ $incident->reporter->nip ?: '-' }})</td>
    </tr>
    <tr>
        <td class="k">Korban Cedera</td>
        <td>{{ $incident->korban ?: 'Tidak Ada Korban' }}</td>
    </tr>
    <tr>
        <td class="k">Saksi di Tempat</td>
        <td>{{ $incident->saksi ?: '-' }}</td>
    </tr>
    <tr>
        <td class="k">Tingkat Keparahan</td>
        <td><span class="badge">{{ $incident->severityLabel() }}</span></td>
    </tr>
    <tr>
        <td class="k">Status Laporan</td>
        <td><span class="badge">{{ $incident->statusLabel() }}</span></td>
    </tr>
    <tr>
        <td class="k">Kronologi Kejadian</td>
        <td>{!! nl2br(e($incident->kronologi)) !!}</td>
    </tr>
    @if($incident->foto)
    <tr>
        <td class="k">Foto Bukti Kamera</td>
        <td>
            <div style="font-size: 11px; color: #004b87; font-weight: bold; margin-bottom: 5px;">[BUKTI KAMERA LANGSUNG TERVERIFIKASI]</div>
            <img src="{{ asset('storage/' . $incident->foto) }}" style="max-height: 200px; max-width: 100%; border: 1px solid #cbd5e1; border-radius: 4px;">
        </td>
    </tr>
    @endif
</table>

<div class="section-heading">II. HASIL INVESTIGASI TIM K3 PUSRI</div>
<table>
    <tr>
        <td class="k">Petugas Verifikasi</td>
        <td>{{ $incident->verifier->name ?? '-' }} @if($incident->verified_at) ({{ $incident->verified_at->format('d/m/Y H:i') }}) @endif</td>
    </tr>
    <tr>
        <td class="k">Penyebab Langsung (Direct Cause)</td>
        <td>{!! nl2br(e($incident->penyebab_langsung ?: '-')) !!}</td>
    </tr>
    <tr>
        <td class="k">Penyebab Dasar (Root Cause)</td>
        <td>{!! nl2br(e($incident->penyebab_dasar ?: '-')) !!}</td>
    </tr>
    <tr>
        <td class="k">Kesimpulan Investigasi</td>
        <td>{!! nl2br(e($incident->hasil_investigasi ?: '-')) !!}</td>
    </tr>
</table>

<div class="section-heading">III. TINDAKAN KOREKTIF &amp; PREVENTIF (CAPA)</div>
<table>
    <thead>
        <tr>
            <th style="width: 50%;">Uraian Tindakan Perbaikan / Pencegahan</th>
            <th style="width: 25%;">Penanggung Jawab</th>
            <th style="width: 15%;">Tenggat</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
    @forelse($incident->actions as $a)
        <tr>
            <td>{{ $a->deskripsi }}</td>
            <td>{{ $a->penanggung_jawab }}</td>
            <td>{{ $a->tenggat->format('d/m/Y') }}</td>
            <td><strong>{{ ucfirst($a->status) }}</strong></td>
        </tr>
    @empty
        <tr>
            <td colspan="4" style="text-align: center; color: #64748b;">Belum ada tindakan yang dicatat.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="signature-box">
    <div class="sig-col">
        <div>Palembang, {{ $incident->tanggal_kejadian->format('d/m/Y') }}</div>
        <div><strong>Pelapor Insiden,</strong></div>
        <div class="sig-space"></div>
        <div><u>{{ $incident->reporter->name }}</u></div>
        <div>NIP: {{ $incident->reporter->nip ?: '-' }}</div>
    </div>
    <div class="sig-col">
        <div>Mengetahui,</div>
        <div><strong>Petugas K3 PT Pusri Palembang,</strong></div>
        <div class="sig-space"></div>
        <div><u>{{ $incident->verifier->name ?? '( ................................... )' }}</u></div>
        <div>Departemen K3 Pusri</div>
    </div>
</div>

<div style="font-size: 10px; color: #94a3b8; margin-top: 30px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 6px;">
    Dokumen dicetak secara otomatis dari Sistem K3 Pusri pada {{ now()->format('d/m/Y H:i:s') }} WIB &bull; Bersama Kita Ciptakan Lingkungan Kerja Aman &amp; Selamat
</div>
</body>
</html>
