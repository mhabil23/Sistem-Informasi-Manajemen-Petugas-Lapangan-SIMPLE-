<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected ?string $webAppUrl;

    public function __construct()
    {
        $this->webAppUrl = config('services.google_sheets.webapp_url', env('GOOGLE_SHEET_WEBAPP_URL'));
    }

    /**
     * Check if Google Sheet Web App URL is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->webAppUrl);
    }

    /**
     * Get the configured Web App URL.
     */
    public function getWebAppUrl(): ?string
    {
        return $this->webAppUrl;
    }

    /**
     * Test connection to the Google Apps Script Web App.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'URL Google Apps Script Web App belum dikonfigurasi di file .env atau Pengaturan.',
            ];
        }

        try {
            $response = Http::timeout(12)->get($this->webAppUrl, [
                'action' => 'ping',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['message'] ?? 'Koneksi ke Google Spreadsheet berhasil terhubung!',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Google Spreadsheet merespons dengan status: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Google Sheet connection error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungi Google Apps Script: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initialize all 14 table sheets in the Google Spreadsheet.
     */
    public function initializeSheets(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'URL Web App belum diatur.'];
        }

        try {
            $response = Http::timeout(25)->post($this->webAppUrl, [
                'action' => 'initSheets',
            ]);

            return $response->json() ?? ['success' => true, 'message' => 'Lembar spreadsheet berhasil diinisialisasi.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch all records from a specific sheet.
     */
    public function getAll(string $sheet, array $params = []): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(15)->get($this->webAppUrl, array_merge([
                'action' => 'getAll',
                'sheet' => $sheet,
            ], $params));

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning("GoogleSheetService getAll error on [{$sheet}]: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Find a record by ID.
     */
    public function getById(string $sheet, $id): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(12)->get($this->webAppUrl, [
                'action' => 'getById',
                'sheet' => $sheet,
                'id' => $id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning("GoogleSheetService getById error on [{$sheet}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Insert a new record into a sheet.
     */
    public function insert(string $sheet, array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'URL Web App belum diatur.'];
        }

        try {
            $response = Http::timeout(15)->post($this->webAppUrl, [
                'action' => 'insert',
                'sheet' => $sheet,
                'data' => json_encode($data),
            ]);

            return $response->json() ?? ['success' => true];
        } catch (\Exception $e) {
            Log::error("GoogleSheetService insert error on [{$sheet}]: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update an existing record in a sheet.
     */
    public function update(string $sheet, $id, array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'URL Web App belum diatur.'];
        }

        try {
            $response = Http::timeout(15)->post($this->webAppUrl, [
                'action' => 'update',
                'sheet' => $sheet,
                'id' => $id,
                'data' => json_encode($data),
            ]);

            return $response->json() ?? ['success' => true];
        } catch (\Exception $e) {
            Log::error("GoogleSheetService update error on [{$sheet}]: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete or soft-delete a record.
     */
    public function delete(string $sheet, $id, bool $soft = true): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'URL Web App belum diatur.'];
        }

        try {
            $response = Http::timeout(15)->post($this->webAppUrl, [
                'action' => 'delete',
                'sheet' => $sheet,
                'id' => $id,
                'soft' => $soft ? 'true' : 'false',
            ]);

            return $response->json() ?? ['success' => true];
        } catch (\Exception $e) {
            Log::error("GoogleSheetService delete error on [{$sheet}]: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Authenticate user against users sheet.
     */
    public function authenticate(string $login, string $password): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(15)->post($this->webAppUrl, [
                'action' => 'authenticate',
                'login' => $login,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['success']) && !empty($data['user'])) {
                    return $data['user'];
                }
            }
        } catch (\Exception $e) {
            Log::error('GoogleSheetService authenticate error: ' . $e->getMessage());
        }

        return null;
    }
}

