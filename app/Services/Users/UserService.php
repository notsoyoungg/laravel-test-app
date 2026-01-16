<?php

namespace App\Services\Users;

use App\Contracts\Repositories\Users\UserRepositoryContract;
use App\DTO\Users\UserDTO;
use Illuminate\Http\UploadedFile;

class UserService
{
    /**
     * UniqueNicknameRule constructor
     *
     * @param UserRepositoryContract $repository
     * @param AvatarsStorageService $service
     */
    public function __construct(
        private readonly UserRepositoryContract $repository,
        private readonly AvatarsStorageService $service
    ) {
    }

    /**
     * Create user
     *
     * @param string $nickname
     * @param UploadedFile $avatar
     * @return UserDTO
     */
    public function create(string $nickname, UploadedFile $avatar): UserDTO
    {
        return $this->repository->create(
            $nickname,
            $this->service->store($avatar)
        );
    }

    /**
     * Get all users
     *
     * @return array
     */
    public function all(): array
    {
        return $this->repository->all();
    }

    /**
     * Get all users
     *
     * @param int $ageInMinutes
     * @return int
     */
    public function deleteOldUsers(int $ageInMinutes): int
    {
        $avatarsPaths = $this->repository->deleteOlderThan($ageInMinutes);

        return $this->service->deleteAvatars($avatarsPaths);
    }
}
