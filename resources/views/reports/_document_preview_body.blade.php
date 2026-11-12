                <!-- Catatan Evaluasi Admin jika ada -->
                <div id="det_admin_notes_box" class="alert alert-warning border-warning p-3 mb-3 d-none">
                    <div class="fw-bold small text-dark mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Catatan Evaluasi Administrator:
                    </div>
                    <div id="det_admin_notes_text" class="small text-secondary"></div>
                </div>

                <!-- Simulation of Official Document Paper -->
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto mb-4" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    @include('reports._kop_surat')

                    <!-- JUDUL DOKUMEN -->
                    <div class="text-center my-3">
                        <h5 class="fw-bold text-decoration-underline mb-0 text-dark" style="font-size: 12pt;">Laporan Perjalanan Dinas</h5>
                    </div>

                    <!-- A. YANG MELAKUKAN PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-3" style="font-size: 10.5pt;">A. Yang Melakukan Perjalanan Dinas</div>
                    <table class="table table-borderless table-sm mb-2 ms-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 170px; padding: 2px 0;">Nama</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="det_officer_name" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;" id="det_label_id">NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_nip" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Pangkat/Golongan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_rank" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_position" style="padding: 2px 0;">-</td>
                        </tr>
                    </table>

                    <!-- B. DASAR PELAKSANAAN -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">B. Dasar Pelaksanaan</div>
                    <ol class="ms-3 mb-2" style="font-size: 10.5pt; padding-left: 20px;">
                        <li id="det_basis_dipa" class="mb-1 text-justify">-</li>
                        <li id="det_basis_st" class="text-justify">-</li>
                    </ol>

                    <!-- C. TUJUAN PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">C. Tujuan Perjalanan Dinas</div>
                    <div id="det_destination_purpose" class="ms-3 mb-2 text-justify" style="font-size: 10.5pt;">-</div>

                    <!-- D. WAKTU PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">D. Waktu Perjalanan Dinas</div>
                    <div id="det_duration_info" class="ms-3 mb-2" style="font-size: 10.5pt;">-</div>

                    <!-- E. HASIL PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">E. Hasil Perjalanan Dinas</div>
                    <div id="det_work_description" class="ms-3 mb-2 text-justify" style="font-size: 10.5pt; white-space: pre-line;">-</div>
                    <div id="det_obstacles_box" class="ms-3 mb-2 d-none" style="font-size: 10.5pt;">
                        <div class="fw-bold">Kendala :</div>
                        <div id="det_obstacles" class="ms-2" style="white-space: pre-line;"></div>
                    </div>
                    <div id="det_solutions_box" class="ms-3 mb-3 d-none" style="font-size: 10.5pt;">
                        <div class="fw-bold">Solusi :</div>
                        <div id="det_solutions" class="ms-2" style="white-space: pre-line;"></div>
                    </div>

                    <!-- F. SARAN DAN REKOMENDASI / TINDAK LANJUT -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">F. Saran dan Rekomendasi / Tindak Lanjut</div>
                    <div id="det_recommendations" class="ms-3 mb-3 text-justify" style="font-size: 10.5pt; white-space: pre-line;">-</div>

                    <!-- TANDA TANGAN -->
                    <div class="row mt-4 pt-2">
                        <div class="col-6"></div>
                        <div class="col-6 text-center" style="font-size: 10.5pt;">
                            <div id="det_signing_place_date">Lasusua, -</div>
                            <div>Pelaksana Perjalanan Dinas,</div>
                            <div id="det_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img or space -->
                            </div>
                            <div class="fw-bold text-dark" id="det_sign_name">-</div>
                            <div style="font-size: 9.5pt;" id="det_sign_nip"></div>
                        </div>
                    </div>
                </div>

                <!-- Simulation of Document Page: Foto Dokumentasi (Lembaran Baru) -->
                <div id="det_docs_paper" class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto mb-4 d-none" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    @include('reports._kop_surat')
                    <div id="det_attachments_container" class="row g-3 justify-content-center pt-2"></div>
                </div>

                <!-- Simulation of Document 2: Daftar Pengeluaran Riil (Tanpa Kop Surat) -->
                <div id="det_dpr_paper" class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto mb-4" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 12pt;">Daftar Pengeluaran Riil</h5>
                    </div>

                    <table class="table table-borderless table-sm mb-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 210px; padding: 2px 0;">Yang bertanda tangan dibawah ini</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td></td>
                        </tr>
                        <tr><td colspan="3" style="height: 6px; padding: 0;"></td></tr>
                        <tr>
                            <td style="padding: 2px 0;">Nama</td>
                            <td style="padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="dpr_officer_name" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="dpr_officer_nik" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="dpr_officer_position" style="padding: 2px 0;">-</td>
                        </tr>
                    </table>

                    <p style="text-align: justify; font-size: 10.5pt; margin-bottom: 14px;">
                        berdasarkan <span id="dpr_st_text">Surat Tugas</span>, dengan ini kami menyatakan dengan sesungguhnya bahwa :
                    </p>

                    <div style="font-size: 10.5pt; margin-bottom: 10px;">
                        1.&nbsp;&nbsp;Biaya transport pegawai di bawah ini yang tidak dapat diperoleh bukti-bukti pengeluarannya, meliputi:
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered border-dark table-sm mb-0" style="font-size: 10pt; border-color: #000 !important;">
                            <thead class="text-center align-middle" style="border-color: #000;">
                                <tr>
                                    <th style="width: 45px; font-weight: normal; border-color: #000;">No</th>
                                    <th style="font-weight: normal; border-color: #000;">Perincian Biaya</th>
                                    <th style="width: 165px; font-weight: normal; border-color: #000;">Jumlah (Rp)</th>
                                </tr>
                                <tr>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(1)</td>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(2)</td>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(3)</td>
                                </tr>
                            </thead>
                            <tbody id="dpr_expense_tbody" style="border-color: #000;"></tbody>
                            <tfoot style="border-color: #000;">
                                <tr>
                                    <td colspan="2" class="text-center fw-bold" style="border-color: #000;">JUMLAH</td>
                                    <td rowspan="2" id="dpr_total_amount" class="text-center align-middle fw-bold" style="border-color: #000;">Rp. 0,-</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center fw-bold fst-italic" style="border-color: #000;">
                                        (<span id="dpr_total_terbilang">Nol Rupiah</span>)
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div style="font-size: 10.5pt; text-align: justify; margin-bottom: 12px;">
                        2.&nbsp;&nbsp;Jumlah uang tersebut pada angka 1 diatas benar-benar dikeluarkan untuk pelaksanaan perjalanan dinas dimaksud dan apabila di kemudian hari terdapat kelebihan atas pembayaran, kami bersedia untuk menyetorkan kelebihan tersebut ke Kas Negara.
                    </div>

                    <p style="font-size: 10.5pt; margin-left: 22px; margin-bottom: 24px;">
                        Demikian pernyataan ini kami buat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <div class="row mt-4" style="font-size: 10.5pt;">
                        <div class="col-6 text-center">
                            <div>Mengetahui/Menyetujui:</div>
                            <div>Pejabat Pembuat Komitmen</div>
                            <div>BPS Kabupaten Kolaka Utara</div>
                            <div style="height: 65px;"></div>
                            <div class="fw-bold text-decoration-underline text-dark" id="dpr_ppk_name">Hidayatullah, SE</div>
                            <div>NIP. <span id="dpr_ppk_nip">198510292011011009</span></div>
                        </div>
                        <div class="col-6 text-center">
                            <div id="dpr_signing_place_date">Lasusua, -</div>
                            <div>Pegawai</div>
                            <div>Yang melakukan perjalanan dinas,</div>
                            <div id="dpr_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img -->
                            </div>
                            <div class="fw-bold text-decoration-underline text-dark" id="dpr_sign_name">-</div>
                            <div id="dpr_sign_nip">-</div>
                        </div>
                    </div>
                </div>

                <!-- Simulation of Surat Pernyataan Paper -->
                <div id="det_vehicle_stmt_paper" class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto d-none" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    @include('reports._kop_surat')

                    <div class="text-center my-3">
                        <h6 class="fw-bold mb-0" style="font-size: 11pt;">SURAT PERNYATAAN</h6>
                        <h6 class="fw-bold text-decoration-underline mb-0" style="font-size: 11pt;">TIDAK MENGGUNAKAN KENDARAAN DINAS</h6>
                    </div>

                    <div class="mb-2" style="font-size: 10.5pt;">Yang bertanda tangan di bawah ini:</div>
                    <table class="table table-borderless table-sm mb-3 ms-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 170px; padding: 2px 0;">Nama</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="stmt_officer_name">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">NIP/NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_nip">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Pangkat/Golongan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_rank">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_position">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Unit Kerja</td>
                            <td style="padding: 2px 0;">:</td>
                            <td>BPS Kabupaten Kolaka Utara</td>
                        </tr>
                    </table>

                    <p style="text-align: justify; font-size: 10.5pt; line-height: 1.5; margin-bottom: 12px;" id="stmt_body_text">
                        Menerangkan bahwa dalam rangka pelaksanaan tugas perjalanan dinas biasa dalam kota, saya benar-benar tidak menggunakan kendaraan dinas.
                    </p>

                    <p style="text-align: justify; font-size: 10.5pt; line-height: 1.5; margin-bottom: 25px;">
                        Demikian pernyataan ini kami buat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya. Apabila terdapat kekeliruan dalam pertanggungjawaban SPD dan mengakibatkan kerugian negara, kami bersedia dituntut sesuai peraturan yang berlaku dan mengembalikan biaya transpor yang sudah terlanjur saya terima ke kas negara.
                    </p>

                    <div class="row mt-4 pt-2">
                        <div class="col-6"></div>
                        <div class="col-6 text-center" style="font-size: 10.5pt;">
                            <div id="stmt_signing_place_date">Lasusua, -</div>
                            <div>Pelaksana Perjalanan Dinas,</div>
                            <div id="stmt_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img or space -->
                            </div>
                            <div class="fw-bold text-dark" id="stmt_sign_name">-</div>
                            <div style="font-size: 9.5pt;" id="stmt_sign_nip"></div>
                        </div>
                    </div>
                </div>

