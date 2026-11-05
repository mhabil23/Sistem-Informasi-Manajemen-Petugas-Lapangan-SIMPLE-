<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class SpreadsheetController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index()
    {
        $isConfigured = $this->sheetService->isConfigured();
        $webAppUrl = $this->sheetService->getWebAppUrl();
        $scriptPath = resource_path('google-apps-script/Code.gs');
        $scriptContent = file_exists($scriptPath) ? file_get_contents($scriptPath) : '';

        return view('admin.spreadsheet.index', compact(
            'isConfigured',
            'webAppUrl',
            'scriptContent'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'webapp_url' => ['nullable', 'url'],
        ], [
            'webapp_url.url' => 'Format URL Web App Google Apps Script tidak valid.',
        ]);

        $url = trim($request->input('webapp_url', ''));

        // Update in .env file
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            if (str_contains($content, 'GOOGLE_SHEET_WEBAPP_URL=')) {
                $content = preg_replace(
                    '/^GOOGLE_SHEET_WEBAPP_URL=.*$/m',
                    'GOOGLE_SHEET_WEBAPP_URL=' . $url,
                    $content
                );
            } else {
                $content .= "\nGOOGLE_SHEET_WEBAPP_URL=" . $url;
            }
            file_put_contents($envPath, $content);
        }

        ActivityLog::log(
            'update',
            'system',
            'Administrator memperbarui konfigurasi URL Database Google Spreadsheet.'
        );

        return back()->with('success', 'URL Google Apps Script Web App berhasil diperbarui!');
    }

    public function testConnection()
    {
        $result = $this->sheetService->testConnection();

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function initSheets()
    {
        $result = $this->sheetService->initializeSheets();

        if ($result['success']) {
            return back()->with('success', $result['message'] ?? '14 Tab Sheet berhasil dibuat di Google Spreadsheet Anda!');
        }

        return back()->with('error', $result['message'] ?? 'Gagal membuat tab di Google Spreadsheet.');
    }
}

