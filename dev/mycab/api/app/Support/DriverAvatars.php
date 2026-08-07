<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DriverAvatars
{
    public const DISK = 'driver_avatars';

    /**
     * Public URL for a stored avatar path (supports legacy storage/app/public paths).
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $filename = basename($path);

        if (Storage::disk(self::DISK)->exists($filename)) {
            return Storage::disk(self::DISK)->url($filename);
        }

        if (Storage::disk(self::DISK)->exists($path)) {
            return Storage::disk(self::DISK)->url($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }

    public static function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $filename = basename($path);

        Storage::disk(self::DISK)->delete($filename);
        Storage::disk(self::DISK)->delete($path);
        Storage::disk('public')->delete($path);
    }

    public static function store(UploadedFile $file): string
    {
        Storage::disk(self::DISK)->makeDirectory('');

        return $file->store('', self::DISK);
    }
}
