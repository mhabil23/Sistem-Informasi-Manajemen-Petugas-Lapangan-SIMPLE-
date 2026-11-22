<div id="terisiSingleSection" class="d-none">
    <!-- PANDUAN FORMAT RESMI BAST DOKUMEN TERISI -->
    <div class="alert alert-info border-0 shadow-sm py-2 px-3 mb-3 rounded-3 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
        <i class="bi bi-info-circle-fill fs-5 text-info flex-shrink-0"></i>
        <div>
            <strong>Format Resmi BAST Hasil Pekerjaan Petugas (Petugas &rarr; Kantor):</strong>
            Isian di bawah otomatis terisi default berdasarkan kegiatan &amp; profil, dan dapat Anda ubah sesuai kondisi riil penugasan lapangan.
        </div>
    </div>

    <!-- 1. KOP & JUDUL DOKUMEN -->
    <div class="card border mb-3 rounded-3">
        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold small text-primary">
                <i class="bi bi-file-earmark-text-fill me-1"></i> 1. KOP &amp; JUDUL DOKUMEN
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Standar BPS</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-secondary">JUDUL DOKUMEN</label>
                    <input type="text" name="document_title" id="terisiDocTitle" class="form-control form-control-sm" value="BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">NOMOR BAST / AGENDA</label>
                    <input type="text" name="bast_number" id="terisiBastNumber" class="form-control form-control-sm" placeholder="Otomatis sistem jika kosong">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">TEMPAT PELAKSANAAN BAST</label>
                    <input type="text" name="document_place" id="terisiDocPlace" class="form-control form-control-sm" value="Badan Pusat Statistik Kabupaten Kolaka Utara" placeholder="Contoh: Badan Pusat Statistik Kabupaten Kolaka Utara">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold text-secondary">NAMA PEKERJAAN / SURVEI LAPANGAN <span class="text-danger">*</span></label>
                    <input type="text" name="activity_work_name" id="terisiActivityWorkName" class="form-control form-control-sm" placeholder="Contoh: Pendataan Lapangan Survei Pertanian Terintegrasi (SITASI) Tahun 2026 pada Badan Pusat Statistik Kabupaten Kolaka Utara">
                    <div class="form-text" style="font-size: 0.7rem;">Otomatis diselaraskan dengan kegiatan yang dipilih jika belum diubah.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- 2. DATA PIHAK PERTAMA (PPK / KANTOR BPS) -->
        <div class="col-md-6">
            <div class="card border h-100 rounded-3">
                <div class="card-header bg-white py-2 px-3">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-building-fill text-secondary me-1"></i> 2. PIHAK PERTAMA (PPK / Kantor BPS)
                    </span>
                </div>
                <div class="card-body p-3">
                    <!-- Dropdown Pilih Pegawai Penandatangan BPS / PPK -->
                    <div class="mb-2.5 p-2 rounded-2 bg-light border border-primary-subtle shadow-sm">
                        <label class="form-label small fw-bold text-primary mb-1 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-person-check-fill me-1"></i> PILIH PEGAWAI PENANDATANGAN</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-normal" style="font-size: 0.65rem;">Otomatis Nama &amp; NIP</span>
                        </label>
                        <select id="terisiPegawaiSelect" class="form-select form-select-sm" onchange="onTerisiPegawaiSelected(this)">
                            <option value="">-- Pilih Nama Pegawai BPS --</option>
                            @foreach(($pegawaiList ?? \App\Models\User::getPegawaiSigners()) as $p)
                                <option value="{{ $p['name'] }}" 
                                        data-name="{{ $p['name'] }}" 
                                        data-nip="{{ $p['nip'] }}" 
                                        data-position="{{ $p['position'] }}"
                                        {{ str_contains($p['name'], 'Hidayatullah') ? 'selected' : '' }}>
                                    {{ $p['name'] }} {{ $p['nip'] ? '('.$p['nip'].')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text mt-1 text-muted" style="font-size: 0.68rem;">
                            <i class="bi bi-info-circle me-0.5"></i> Memilih pegawai akan otomatis mengisi <strong>Nama</strong> dan <strong>NIP</strong> di bawah (tetap dapat Anda edit).
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Nama PPK / Pihak Pertama <span class="text-danger">*</span></label>
                        <input type="text" name="maker_name" id="terisiMakerName" class="form-control form-control-sm" value="Hidayatullah, SE" placeholder="Nama lengkap & gelar">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">NIP PPK</label>
                        <input type="text" name="maker_nip" id="terisiMakerNip" class="form-control form-control-sm font-monospace" value="198510292011011009" placeholder="NIP 18 digit">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Jabatan</label>
                        <input type="text" name="first_party_position" id="terisiFirstPartyPosition" class="form-control form-control-sm" value="Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Unit Kerja</label>
                        <input type="text" name="institution_name" id="terisiInstitutionName" class="form-control form-control-sm" value="BPS Kabupaten Kolaka Utara">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Alamat Kantor / Unit Kerja</label>
                        <input type="text" name="first_party_address" id="terisiFirstPartyAddress" class="form-control form-control-sm" value="Jalan Protokol Poros DPRD, Kec. Lasusua, Kolaka Utara Sulawesi Tenggara">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. DATA PIHAK KEDUA (PETUGAS LAPANGAN) -->
        <div class="col-md-6">
            <div class="card border h-100 rounded-3">
                <div class="card-header bg-white py-2 px-3">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-person-fill text-primary me-1"></i> 3. PIHAK KEDUA (Petugas Lapangan)
                    </span>
                </div>
                <div class="card-body p-3">
                    @if(isset($isAdminForm) && $isAdminForm)
                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-secondary">Pilih Petugas Lapangan <span class="text-danger">*</span></label>
                            <select name="officer_id" id="bastSingleOfficerInput" class="form-select form-select-sm" onchange="onTerisiOfficerChanged(this)">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" 
                                            data-name="{{ $officer->name }}" 
                                            data-nik="{{ $officer->nik ?? '' }}" 
                                            data-address="{{ $officer->address ?? 'Kecamatan Lasusua' }}">
                                        {{ $officer->name }} ({{ $officer->phone ?? $officer->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Nama Petugas Lapangan <span class="text-danger">*</span></label>
                        <input type="text" name="second_party_name" id="terisiSecondPartyName" class="form-control form-control-sm" value="{{ auth()->user()->name ?? '' }}" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">NIK Petugas (KTP) <span class="text-danger">*</span></label>
                        <input type="text" name="second_party_nik" id="terisiSecondPartyNik" class="form-control form-control-sm font-monospace" value="{{ auth()->user()->nik ?? '' }}" placeholder="NIK 16 digit">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Alamat Domisili Petugas <span class="text-danger">*</span></label>
                        <input type="text" name="second_party_address" id="terisiSecondPartyAddress" class="form-control form-control-sm" value="{{ auth()->user()->address ?? 'Kecamatan Lasusua' }}" placeholder="Contoh: Desa Watuliu, Kecamatan Lasusua">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. KLAUSUL PERJANJIAN & JUMLAH HASIL DOKUMEN -->
    <div class="card border rounded-3 mb-2">
        <div class="card-header bg-white py-2 px-3">
            <span class="fw-bold small text-dark">
                <i class="bi bi-file-earmark-check-fill text-success me-1"></i> 4. KLAUSUL PERJANJIAN KERJA &amp; HASIL PEKERJAAN
            </span>
        </div>
        <div class="card-body p-3">
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-secondary">Nomor Perjanjian Kerja / SPK <span class="text-danger">*</span></label>
                    <input type="text" name="contract_number" id="terisiContractNumber" class="form-control form-control-sm font-monospace" placeholder="Contoh: PPK 7408/06/10/2026/001">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Perjanjian / SPK <span class="text-danger">*</span></label>
                    <input type="date" name="contract_date" id="terisiContractDate" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Jumlah Dokumen / Berkas <span class="text-danger">*</span></label>
                    <input type="text" name="doc_count" id="terisiDocCount" class="form-control form-control-sm" value="30 Dokumen" placeholder="Contoh: 30 Dokumen / 120 Berkas">
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label small fw-semibold text-secondary">Keterangan / Rincian Penyerahan Dokumen (Opsional)</label>
                <textarea name="description" id="bastSingleDescriptionInput" class="form-control form-control-sm" rows="2" placeholder="Catatan berkas yang diserahkan..."></textarea>
            </div>
        </div>
    </div>
</div>
