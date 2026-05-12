<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Dokumentasi Kefarmasian</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

    <nav class="no-print main-nav">
        <button onclick="showForm('pio')" id="btn-pio">1. Dokumentasi PIO</button>
        <button onclick="showForm('catatan')" id="btn-catatan">2. Catatan Pengobatan</button>
        <button onclick="showForm('homecare')" id="btn-homecare">3. Home Pharmacy Care</button>
    </nav>

    <div class="container">
        <section id="form-pio" class="form-page">
            <div class="page-content">
                <p class="form-code">Formulir 8. Dokumentasi PIO</p>
                <div class="outer-border">
                    <h2 class="title-box">DOKUMENTASI PELAYANAN INFORMASI OBAT (PIO)</h2>
                    
                    <div class="row border-bottom">
                        <div class="col">No. <input type="text" class="input-inline save-local" data-key="pio_no"></div>
                        <div class="col">Tanggal: <input type="date" class="input-inline save-local" data-key="pio_tgl"></div>
                        <div class="col">Waktu: <input type="time" class="input-inline save-local" data-key="pio_waktu"></div>
                        <div class="col">Metode: <select class="save-local" data-key="pio_metode"><option>Lisan</option><option>Tertulis</option><option>Telepon</option></select></div>
                    </div>

                    <div class="section-label">Identitas Penanya</div>
                    <div class="padding-box">
                        <div class="row">
                            <div class="col-2">Nama: <input type="text" class="input-inline save-local" data-key="pio_nama_penanya"></div>
                            <div class="col-1">No. Telp: <input type="text" class="input-inline save-local" data-key="pio_telp_penanya"></div>
                        </div>
                        <p>Status: <label><input type="radio" name="st" class="save-local" data-key="pio_st_p"> Pasien</label> / <label><input type="radio" name="st"> Keluarga</label> / <label><input type="radio" name="st"> Petugas Kesehatan</label> (<input type="text" class="input-inline save-local" data-key="pio_st_ket" style="width:200px">)*</p>
                    </div>

                    <div class="section-label">Data Pasien</div>
                    <div class="padding-box">
                        <p>Umur: <input type="number" class="input-inline save-local" data-key="pio_umur" style="width:50px"> thn; Tinggi: <input type="number" class="input-inline save-local" data-key="pio_tb" style="width:50px"> cm; Berat: <input type="number" class="input-inline save-local" data-key="pio_bb" style="width:50px"> kg;</p>
                        <p>Jenis Kelamin: <label><input type="radio" name="jk"> L</label> / <label><input type="radio" name="jk"> P</label> | Hamil: <input type="text" class="input-inline" style="width:30px"> mgg | Menyusui: Ya/Tidak</p>
                    </div>

                    <div class="section-label">Pertanyaan</div>
                    <div class="padding-box">
                        <textarea class="auto-line save-local" data-key="pio_tanya" rows="3" placeholder="Uraian Pertanyaan..."></textarea>
                    </div>

                    <div class="section-label">Jawaban & Referensi</div>
                    <div class="padding-box">
                        <textarea class="auto-line save-local" data-key="pio_jawab" rows="5" placeholder="Jawaban..."></textarea>
                        <p style="margin-top:10px">Referensi: <input type="text" class="input-inline save-local" data-key="pio_ref" style="width:80%"></p>
                    </div>

                    <div class="footer-sign">
                        <p>Apoteker yang menjawab:</p>
                        <br><br>
                        <input type="text" class="input-inline save-local" data-key="pio_apt" style="width:200px; text-align:center; font-weight:bold" placeholder="(Nama Apoteker)">
                    </div>
                </div>
            </div>
        </section>

        <section id="form-catatan" class="form-page" style="display:none">
            <div class="page-content">
                <h2 class="center-title">CATATAN PENGOBATAN PASIEN</h2>
                <div class="id-grid">
                    <span>Nama Pasien : <input type="text" class="input-inline save-local" data-key="c_nama"></span>
                    <span>Umur : <input type="text" class="input-inline save-local" data-key="c_umur"></span>
                    <span>Jenis Kelamin : <input type="text" class="input-inline save-local" data-key="c_jk"></span>
                    <span>No. Telp : <input type="text" class="input-inline save-local" data-key="c_telp"></span>
                    <span style="grid-column: span 2">Alamat : <input type="text" class="input-inline save-local" data-key="c_alamat"></span>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Dokter</th>
                            <th>Nama Obat / Dosis / Cara Pemberian</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-catatan">
                        </tbody>
                </table>
                <button class="no-print btn-add" onclick="addRow('tbody-catatan')">+ Tambah Baris</button>
            </div>
        </section>

        <section id="form-homecare" class="form-page" style="display:none">
            <div class="page-content">
                <h2 class="center-title">DOKUMENTASI PELAYANAN KEFARMASIAN DI RUMAH<br>(HOME PHARMACY CARE)</h2>
                <div class="id-grid">
                    <span>Nama Pasien : <input type="text" class="input-inline save-local" data-key="h_nama"></span>
                    <span>Umur : <input type="text" class="input-inline save-local" data-key="h_umur"></span>
                    <span>Alamat : <input type="text" class="input-inline save-local" data-key="h_alamat"></span>
                    <span>No. Telepon : <input type="text" class="input-inline save-local" data-key="h_telp"></span>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="150">Tgl. Kunjungan</th>
                            <th>Catatan Apoteker</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-homecare">
                        </tbody>
                </table>
                <button class="no-print btn-add" onclick="addRow('tbody-homecare')">+ Tambah Baris</button>
            </div>
        </section>
    </div>

    <div class="no-print floating-tools">
        <button onclick="exportPDF()" class="btn-pdf">Simpan PDF</button>
        <button onclick="clearAllData()" class="btn-reset">Reset Form</button>
    </div>

    <script src="script.js"></script>
</body>
</html>