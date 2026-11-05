/**
 * =========================================================================
 * SIMPLE - Google Apps Script Database Engine
 * Sistem Informasi Administrasi & Kegiatan SIMPLE
 * =========================================================================
 * 
 * PETUNJUK PEMASANGAN:
 * 1. Buat Google Spreadsheet baru di Google Drive Anda (misal: "Database SIMPLE").
 * 2. Klik menu "Ekstensi" (Extensions) > "Apps Script".
 * 3. Hapus kode bawaan, lalu salin dan tempel SELURUH KODE ini ke Apps Script.
 * 4. Klik tombol "Simpan" (ikon disket).
 * 5. Klik tombol biru "Terapkan" (Deploy) di kanan atas > "Deployment Baru" (New deployment).
 * 6. Pilih jenis: "Aplikasi Web" (Web app).
 * 7. Isi Konfigurasi:
 *    - Deskripsi: API Database SIMPLE
 *    - Jalankan sebagai: "Saya" (Me)
 *    - Yang memiliki akses: "Siapa saja" (Anyone) -> SANGAT PENTING agar website Laravel bisa terhubung!
 * 8. Klik "Terapkan" (Deploy), berikan izin akses Google jika diminta.
 * 9. Salin "URL Aplikasi Web" (Web App URL) yang berakhiran `/exec`.
 * 10. Masukkan URL tersebut ke Pengaturan Spreadsheet di website SIMPLE.
 */

// Daftar 14 Sheet / Tabel SIMPLE dan Header Kolomnya
const SHEET_SCHEMAS = {
  users: ['id', 'name', 'username', 'email', 'phone', 'password', 'role', 'status', 'created_at', 'updated_at', 'deleted_at'],
  assignments: ['id', 'title', 'description', 'start_date', 'end_date', 'location', 'status', 'created_by', 'created_at', 'updated_at', 'deleted_at'],
  assignment_officers: ['id', 'assignment_id', 'user_id', 'specific_task', 'status', 'progress_notes', 'created_at', 'updated_at'],
  respondents: ['id', 'name', 'address', 'phone', 'agency', 'purpose', 'assignment_id', 'officer_id', 'input_date', 'created_at', 'updated_at', 'deleted_at'],
  respondent_basts: ['id', 'bast_number', 'bast_date', 'respondent_id', 'assignment_id', 'officer_id', 'handover_items', 'notes', 'file_path', 'status', 'created_at', 'updated_at', 'deleted_at'],
  officer_basts: ['id', 'bast_number', 'bast_date', 'assignment_id', 'officer_id', 'description', 'notes', 'file_path', 'status', 'created_at', 'updated_at', 'deleted_at'],
  officer_reports: ['id', 'report_number', 'user_id', 'assignment_id', 'activity_date', 'activity_title', 'work_description', 'status', 'admin_notes', 'reviewed_by', 'reviewed_at', 'created_at', 'updated_at', 'deleted_at'],
  report_attachments: ['id', 'report_id', 'file_name', 'file_path', 'file_type', 'file_size', 'caption', 'created_at', 'updated_at'],
  training_events: ['id', 'title', 'description', 'start_date', 'end_date', 'location', 'quota', 'status', 'created_by', 'created_at', 'updated_at', 'deleted_at'],
  training_participants: ['id', 'training_event_id', 'name', 'nip_or_id', 'category', 'agency', 'phone', 'email', 'attendance_status', 'certificate_number', 'created_at', 'updated_at', 'deleted_at'],
  document_templates: ['id', 'code', 'name', 'type', 'content', 'header_text', 'footer_text', 'is_active', 'created_at', 'updated_at'],
  generated_documents: ['id', 'document_number', 'title', 'document_type', 'user_id', 'documentable_type', 'documentable_id', 'file_path', 'notes', 'created_at', 'updated_at'],
  schedules: ['id', 'title', 'assignment_id', 'officer_id', 'start_time', 'end_time', 'location', 'notes', 'status', 'created_at', 'updated_at', 'deleted_at'],
  activity_logs: ['id', 'user_id', 'action', 'module', 'description', 'ip_address', 'user_agent', 'created_at', 'updated_at']
};

function doGet(e) {
  const params = e.parameter || {};
  const action = params.action || 'ping';

  if (action === 'ping') {
    return jsonResponse({
      success: true,
      message: 'Koneksi ke Google Spreadsheet SIMPLE Berhasil Terhubung!',
      timestamp: new Date().toISOString()
    });
  }

  if (action === 'initSheets') {
    return jsonResponse(initAllSheets());
  }

  if (action === 'getAll') {
    const sheetName = params.sheet;
    return jsonResponse(getAllRows(sheetName, params));
  }

  if (action === 'getById') {
    const sheetName = params.sheet;
    const id = params.id;
    return jsonResponse(getRowById(sheetName, id));
  }

  return jsonResponse({ success: false, message: 'Action tidak dikenal: ' + action });
}

function doPost(e) {
  let params = {};
  try {
    if (e.postData && e.postData.contents) {
      if (e.postData.type === 'application/json') {
        params = JSON.parse(e.postData.contents);
      } else {
        params = e.parameter || {};
        if (params.data && typeof params.data === 'string') {
          try { params.data = JSON.parse(params.data); } catch (err) {}
        }
      }
    } else {
      params = e.parameter || {};
    }
  } catch (err) {
    params = e.parameter || {};
  }

  const action = params.action || '';

  if (action === 'initSheets') {
    return jsonResponse(initAllSheets());
  }

  if (action === 'insert') {
    return jsonResponse(insertRow(params.sheet, params.data));
  }

  if (action === 'update') {
    return jsonResponse(updateRow(params.sheet, params.id, params.data));
  }

  if (action === 'delete') {
    return jsonResponse(deleteRow(params.sheet, params.id, params.soft === 'true' || params.soft === true));
  }

  return jsonResponse({ success: false, message: 'Action POST tidak dikenal.' });
}

// Inisialisasi Seluruh 14 Tab Sheet
function initAllSheets() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const createdSheets = [];

  for (const sheetName in SHEET_SCHEMAS) {
    let sheet = ss.getSheetByName(sheetName);
    const headers = SHEET_SCHEMAS[sheetName];

    if (!sheet) {
      sheet = ss.insertSheet(sheetName);
      sheet.appendRow(headers);
      // Format baris header
      const headerRange = sheet.getRange(1, 1, 1, headers.length);
      headerRange.setBackground('#0d2142');
      headerRange.setFontColor('#ffffff');
      headerRange.setFontWeight('bold');
      sheet.setFrozenRows(1);
      createdSheets.push(sheetName);
    }
  }

  // Hapus 'Sheet1' bawaan jika kosong
  const defaultSheet = ss.getSheetByName('Sheet1') || ss.getSheetByName('Sheet 1');
  if (defaultSheet && defaultSheet.getLastRow() === 0) {
    try { ss.deleteSheet(defaultSheet); } catch (e) {}
  }

  // Tambahkan akun default jika sheet users masih kosong
  const userSheet = ss.getSheetByName('users');
  if (userSheet && userSheet.getLastRow() <= 1) {
    const now = new Date().toISOString();
    userSheet.appendRow([1, 'Administrator SIMPLE', 'admin', 'admin@simple.go.id', '081234567890', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oUetqAHRfODsfBVrr6N.ZkLg2bB1G', 'admin', 'active', now, now, '']);
    userSheet.appendRow([2, 'Budi Santoso, S.Kom', 'petugas1', 'petugas1@simple.go.id', '081234567891', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oUetqAHRfODsfBVrr6N.ZkLg2bB1G', 'petugas', 'active', now, now, '']);
    userSheet.appendRow([3, 'Siti Rahmawati, S.Tr.Stat', 'petugas2', 'petugas2@simple.go.id', '081234567892', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oUetqAHRfODsfBVrr6N.ZkLg2bB1G', 'petugas', 'active', now, now, '']);
  }

  return {
    success: true,
    message: '14 Tab Sheet Database SIMPLE berhasil disiapkan dengan skema lengkap!',
    sheets: Object.keys(SHEET_SCHEMAS)
  };
}

// Ambil seluruh baris
function getAllRows(sheetName, filterParams) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName(sheetName);
  if (!sheet) return { success: false, message: 'Sheet tidak ditemukan: ' + sheetName, data: [] };

  const lastRow = sheet.getLastRow();
  const lastCol = sheet.getLastColumn();
  if (lastRow <= 1) return { success: true, data: [] };

  const data = sheet.getRange(1, 1, lastRow, lastCol).getValues();
  const headers = data[0];
  const rows = [];

  for (let i = 1; i < data.length; i++) {
    const rowObj = {};
    for (let j = 0; j < headers.length; j++) {
      rowObj[headers[j]] = data[i][j];
    }

    // Filter soft deleted jika diminta
    if (filterParams.include_deleted !== 'true' && rowObj.deleted_at) {
      continue;
    }

    rows.push(rowObj);
  }

  return { success: true, count: rows.length, data: rows };
}

// Ambil baris berdasarkan ID
function getRowById(sheetName, id) {
  const res = getAllRows(sheetName, { include_deleted: 'false' });
  if (!res.success) return res;

  const found = res.data.find(r => String(r.id) === String(id));
  return { success: !!found, data: found || null };
}

// Tambah baris baru
function insertRow(sheetName, rowData) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = ss.getSheetByName(sheetName);
  if (!sheet) {
    initAllSheets();
    sheet = ss.getSheetByName(sheetName);
  }

  const headers = SHEET_SCHEMAS[sheetName] || sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  
  // Tentukan ID berikutnya (auto-increment)
  const lastRow = sheet.getLastRow();
  let nextId = 1;
  if (lastRow > 1) {
    const idColValues = sheet.getRange(2, 1, lastRow - 1, 1).getValues();
    const ids = idColValues.map(v => parseInt(v[0])).filter(v => !isNaN(v));
    if (ids.length > 0) {
      nextId = Math.max(...ids) + 1;
    }
  }

  const now = new Date().toISOString();
  if (typeof rowData === 'string') {
    try { rowData = JSON.parse(rowData); } catch (e) {}
  }
  rowData = rowData || {};
  rowData.id = nextId;
  rowData.created_at = rowData.created_at || now;
  rowData.updated_at = now;

  const newRow = [];
  for (let i = 0; i < headers.length; i++) {
    const key = headers[i];
    newRow.push(rowData[key] !== undefined ? rowData[key] : '');
  }

  sheet.appendRow(newRow);
  return { success: true, message: 'Data berhasil disimpan ke Spreadsheet!', id: nextId, data: rowData };
}

// Perbarui baris
function updateRow(sheetName, id, updateData) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName(sheetName);
  if (!sheet) return { success: false, message: 'Sheet tidak ditemukan.' };

  const lastRow = sheet.getLastRow();
  if (lastRow <= 1) return { success: false, message: 'Data kosong.' };

  const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  const idCol = sheet.getRange(2, 1, lastRow - 1, 1).getValues();

  let targetRowIndex = -1;
  for (let i = 0; i < idCol.length; i++) {
    if (String(idCol[i][0]) === String(id)) {
      targetRowIndex = i + 2; // offset header dan 1-based index
      break;
    }
  }

  if (targetRowIndex === -1) {
    return { success: false, message: 'Data dengan ID ' + id + ' tidak ditemukan.' };
  }

  if (typeof updateData === 'string') {
    try { updateData = JSON.parse(updateData); } catch (e) {}
  }
  updateData = updateData || {};
  updateData.updated_at = new Date().toISOString();

  for (let j = 0; j < headers.length; j++) {
    const key = headers[j];
    if (updateData[key] !== undefined && key !== 'id') {
      sheet.getRange(targetRowIndex, j + 1).setValue(updateData[key]);
    }
  }

  return { success: true, message: 'Data di Spreadsheet berhasil diperbarui.' };
}

// Hapus baris (soft delete atau hard delete)
function deleteRow(sheetName, id, isSoft) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName(sheetName);
  if (!sheet) return { success: false, message: 'Sheet tidak ditemukan.' };

  const lastRow = sheet.getLastRow();
  const idCol = sheet.getRange(2, 1, lastRow - 1, 1).getValues();

  let targetRowIndex = -1;
  for (let i = 0; i < idCol.length; i++) {
    if (String(idCol[i][0]) === String(id)) {
      targetRowIndex = i + 2;
      break;
    }
  }

  if (targetRowIndex === -1) {
    return { success: false, message: 'Data tidak ditemukan.' };
  }

  if (isSoft) {
    const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
    const deletedAtIndex = headers.indexOf('deleted_at');
    if (deletedAtIndex !== -1) {
      sheet.getRange(targetRowIndex, deletedAtIndex + 1).setValue(new Date().toISOString());
      return { success: true, message: 'Data berhasil diarsipkan (soft deleted).' };
    }
  }

  sheet.deleteRow(targetRowIndex);
  return { success: true, message: 'Baris data berhasil dihapus permanen dari Spreadsheet.' };
}

function jsonResponse(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}

