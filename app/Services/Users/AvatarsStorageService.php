<?php

namespace App\Services\Users;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarsStorageService
{
    /**
     * Disk name
     */
    private const string DISK_NAME = 'public';

    /**
     * Path to avatars
     */
    private const string BASE_PATH = 'avatars';

    /**
     * Store avatar
     *
     * @param UploadedFile $avatar
     * @return false|string
     */
    public function store(UploadedFile $avatar): false|string
    {
        return $avatar->store(self::BASE_PATH, self::DISK_NAME);
    }

    /**
     * Delete old avatars
     *
     * @param array $paths
     * @return int
     */
    public function deleteAvatars(array $paths): int
    {
        if ($paths === []) {
            return 0;
        }

        Storage::disk(self::DISK_NAME)->delete($paths);

        return count($paths);
    }
}
