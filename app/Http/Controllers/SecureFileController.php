<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves private files (signatures, government IDs, contracts) through
 * authenticated routes instead of exposing them via public storage URLs.
 */
class SecureFileController extends Controller
{
    /**
     * Serve a file from the private disk.
     *
     * Accepts a path like: /secure/file/signatures/123_movein_owner_1712345678.png
     * Only authenticated users can access these files.
     */
    public function serve(string $path): Response
    {
        // Prevent directory traversal
        $path = str_replace(['..', '\\'], ['', '/'], $path);

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $mimeType = Storage::disk('local')->mimeType($path);

        return response(Storage::disk('local')->get($path), 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
