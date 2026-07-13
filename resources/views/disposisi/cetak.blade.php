<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->perihal }}-{{ $surat->pengirim }}-{{ \Carbon\Carbon::parse($surat->tgl_surat)->format('d-m-Y') }}-SEKPER</title>
    <style>
        /* Memaksa kertas A4 Landscape (Menyamping) */
        @page { size: A4 landscape; margin: 10mm; }
        
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 8pt; 
            color: #000; 
            background: #fff; 
            line-height: 1.2; 
            margin: 0;
        }
        
        /* Kontainer membatasi konten HANYA di sebelah KIRI (Setengah Kertas A4) */
        .kertas-kiri {
            width: 135mm; /* Setengah dari lebar A4 (297mm) */
            height: 190mm; /* Tinggi maksimal area cetak */
            float: left; /* Rata Kiri */
            padding-right: 5mm;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        th, td { border: 1px solid #000; padding: 2px 4px; vertical-align: middle; }
        
        .no-border { border: none !important; }
        .no-border td { border: none !important; padding: 1px 2px; }
        
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .header-title { font-size: 12pt; font-weight: bold; text-align: center; }
        
        /* Checkbox kotak */
        .box { 
            display: inline-block; 
            width: 10px; height: 10px; 
            border: 1px solid #000; 
            margin-right: 4px; 
            position: relative; 
            top: 2px; 
        }
        .box.checked::after { 
            content: '✓'; 
            position: absolute; 
            top: -4px; left: 1px; 
            font-size: 11px; font-weight: bold; 
        }

        .garis-bawah { border-bottom: 1.5px solid #000 !important; font-weight: bold; }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px; text-align: center; width: 100%; clear: both;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 12px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 4px;">Cetak Disposisi</button>
    </div>

    <div class="kertas-kiri">
        
        <table class="no-border" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 35%; text-align: left; vertical-align: middle;">
                    <div class="header-title">LEMBAR DISPOSISI</div>
                </td>
                <td style="width: 20%; text-align: center; vertical-align: middle;">
                    <img src="{{ asset('images/logo-ptpn4.png') }}" alt="Logo PTPN IV" style="width: 40px; height: auto;">
                </td>
                <td style="width: 45%; text-align: right; vertical-align: bottom; font-size: 8.5pt;">
                    Nomor Agenda Penerimaan : <strong>{{ $surat->no_agenda ?? '....' }}</strong>
                </td>
            </tr>
        </table>

        <table class="no-border" style="margin-bottom: 6px; width: 100%;">
            <tr>
                <td style="width: 15%;"><div class="box"></div> Email</td>
                <td style="width: 18%;"><div class="box"></div> Siskomdat</td>
                <td style="width: 15%;"><div class="box"></div> Faximile</td>
                <td style="width: 22%;"><div class="box"></div> ....................</td>
                <td style="width: 15%;"><div class="box"></div> Copy</td>
                <td style="width: 15%;"><div class="box"></div> Tembusan</td>
            </tr>
            <tr>
                <td><div class="box {{ $surat->jenis_surat == 'internal' ? 'checked' : '' }}"></div> Surat</td>
                <td><div class="box"></div> Proposal</td>
                <td><div class="box"></div> Undangan</td>
                <td><div class="box checked"></div> Asli</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><div class="box"></div> Penting</td>
                <td><div class="box"></div> Segera</td>
                <td><div class="box"></div> Rahasia</td>
                <td><div class="box"></div> Biasa</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="no-border" style="margin-bottom: 6px;">
            <tr>
                <td style="width: 40px; font-weight: bold;">Dari</td>
                <td style="width: 10px; font-weight: bold;">:</td>
                <td class="garis-bawah">{{ $surat->pengirim }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nomor</td>
                <td style="font-weight: bold;">:</td>
                <td class="garis-bawah">{{ $surat->no_surat }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Hal</td>
                <td style="font-weight: bold;">:</td>
                <td class="garis-bawah">{{ $surat->perihal }}</td>
            </tr>
        </table>

        @php
            $tujuan = optional($surat->disposition)->tujuan_head ?? $surat->tujuan_head ?? '';
        @endphp
        
        <table style="font-size: 7.5pt;">
            <tr class="text-center font-bold">
                <td rowspan="2" colspan="3" style="width: 42%;">Diteruskan Kepada</td>
                <td rowspan="2" style="width: 6%;">No.<br>Agd</td>
                <td colspan="2" style="width: 14%;">Tanggal</td>
                <td rowspan="2" style="width: 8%;">Paraf</td>
                <td rowspan="2" style="width: 30%;">Disposisi</td>
            </tr>
            <tr class="text-center font-bold">
                <td style="width: 7%;">Masuk</td>
                <td style="width: 7%;">Keluar</td>
            </tr>
            
            <tr><td class="text-center" style="width: 5%;">RHE</td><td style="width: 32%;">REGION HEAD</td><td class="text-center font-bold" style="width: 5%;">{{ $tujuan == 'RHE' ? '√' : '' }}</td><td></td><td></td><td></td><td></td><td><div class="box"></div> Dilaksanakan</td></tr>
            <tr><td class="text-center">OH</td><td>OPERATION HEAD</td><td class="text-center font-bold">{{ $tujuan == 'OH' ? '√' : '' }}</td><td></td><td></td><td></td><td></td><td><div class="box"></div> Dipersiapkan</td></tr>
            <tr><td class="text-center">BSH</td><td>BUSINESS SUPPORT HEAD</td><td class="text-center font-bold">{{ $tujuan == 'BSH' ? '√' : '' }}</td><td></td><td></td><td></td><td></td><td><div class="box"></div> Diteruskan</td></tr>
            <tr><td class="text-center">SKH</td><td>BAG. SEKRETARIAT DAN HUKUM</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Ditelaah/Dipelajari</td></tr>
            <tr><td class="text-center">SPI</td><td>BAG. SATUAN PENGAWAS INTERNAL</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Dipedomani/Diedarkan</td></tr>
            <tr><td class="text-center">TAN</td><td>BAG. TANAMAN</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Diteliti/Dijawab/Segera</td></tr>
            <tr><td class="text-center">TEP</td><td>BAG. TEKNIK DAN PENGOLAHAN</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Diikuti Perkembangannya</td></tr>
            <tr><td class="text-center">AKN</td><td>BAG. AKUNTANSI DAN KEUANGAN</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Proses Sesuai Ketentuan</td></tr>
            <tr><td class="text-center">SDM</td><td>BAG. SDM & SISTEM MANAJEMEN</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Setuju Ditindaklanjuti</td></tr>
            <tr><td class="text-center">PTI</td><td>BAG. PENGADAAN DAN TI</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Tidak Setuju</td></tr>
            <tr><td class="text-center">PMO</td><td>BAG. PROJECT MANAGEMENT OFFICE</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Bicarakan Dengan Saya</td></tr>
            <tr><td class="text-center">DBR</td><td>DISTRIK BARAT</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Saran/Tanggapan</td></tr>
            <tr><td class="text-center">DTM</td><td>DISTRIK TIMUR</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Saya Hadir</td></tr>
            <tr><td class="text-center">DPM</td><td>DISTRIK PETANI MITRA</td><td></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Dihadiri/Diwakilkan</td></tr>
            <tr><td colspan="3"></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Tunda pelaksanaan</td></tr>
            <tr><td colspan="3"></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Diketahui</td></tr>
            <tr><td colspan="3"></td><td></td><td></td><td></td><td></td><td><div class="box"></div> Arsip</td></tr>
        </table>

        <table style="margin-top: 4px;">
            <tr>
                <td style="text-align: center; height: 100px; vertical-align: top; padding-top: 4px;">
                    Catatan
                </td>
            </tr>
        </table>
        
        <div style="font-size: 7pt; font-style: italic; margin-top: 2px;">
            AKHLAK-Amanah-Kompeten-Harmonis-Loyal-Adaptif-Kolaboratif
        </div>
        
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>