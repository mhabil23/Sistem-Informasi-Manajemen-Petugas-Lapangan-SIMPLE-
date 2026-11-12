<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class StorageFileController extends Controller
{
    public function show(string $path)
    {
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        abort_if($cleanPath === '' || str_contains($cleanPath, '..'), 404);

        $fileName = basename($cleanPath);
        abort_if($fileName === '' || str_starts_with($fileName, '.'), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($cleanPath), 404);

        $baseRoot = realpath(storage_path('app/public'));
        $fullPath = realpath($disk->path($cleanPath));
        abort_if(
            $baseRoot === false
            || $fullPath === false
            || !is_file($fullPath)
            || !str_starts_with($fullPath, $baseRoot . DIRECTORY_SEPARATOR),
            404
        );

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

