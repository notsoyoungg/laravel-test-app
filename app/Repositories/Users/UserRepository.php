<?php

namespace App\Repositories\Users;

use App\Contracts\Repositories\Users\UserRepositoryContract;
use App\DTO\Users\UserDTO;
use Illuminate\Support\Facades\Redis;

class UserRepository implements UserRepositoryContract
{
    /**
     * Hash key
     */
    protected const string HASH_KEY = 'users';

    /**
     * Sorted key
     */
    protected const string SORTED_KEY = 'users:created';


    /**
     * Check that the user exists
     *
     * @param string $nickname
     * @return bool
     */
    public function exists(string $nickname): bool
    {
        return Redis::hexists(self::HASH_KEY, $nickname);
    }

    /**
     * Create user
     *
     * @param string $nickname
     * @param string $avatar
     * @return UserDTO
     */
    public function create(string $nickname, string $avatar): UserDTO
    {
        $userDto = new UserDTO(
            $nickname,
            $avatar,
            now()->timestamp
        );

        Redis::multi()
            ->hset(self::HASH_KEY, $nickname, json_encode($userDto->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
            ->zadd(
                self::SORTED_KEY,
                $userDto->createdAt,
                $nickname
            )
            ->exec();

        return $userDto;
    }

    /**
     * Get all users
     *
     * @return array
     */
    public function all(): array
    {
        $nicknames = Redis::zrange(self::SORTED_KEY, 0, -1);

        if (empty($nicknames)) {
            return [];
        }

        $rawUsers = Redis::hmget(self::HASH_KEY, $nicknames);

        $users = [];

        foreach ($rawUsers as $rawUser) {
            if (!$rawUser) {
                continue;
            }

            $data = json_decode($rawUser, true);

            $users[] = UserDTO::fromArray($data);
        }

        return $users;
    }

    /**
     * Delete old users data
     *
     * @param int $minutes
     * @param int $batchSize
     * @return array
     */
    public function deleteOlderThan(int $minutes, int $batchSize = 1000): array
    {
        $threshold = now()->subMinutes($minutes)->timestamp;

        $avatarsToDelete = [];

        while (true) {
            $expiredNicknames = Redis::zrangebyscore(
                self::SORTED_KEY,
                '-inf',
                $threshold,
                ['limit' => [0, $batchSize]]
            );

            if (empty($expiredNicknames)) {
                break;
            }

            $usersData = Redis::hmget(self::HASH_KEY, $expiredNicknames);

            foreach ($usersData as $userData) {
                if (!$userData) {
                    continue;
                }

                $user = json_decode($userData, true);
                if (!empty($user['avatar'])) {
                    $avatarsToDelete[] = $user['avatar'];
                }
            }

            Redis::pipeline(function ($pipe) use ($expiredNicknames) {
                $pipe->hdel(self::HASH_KEY, ...$expiredNicknames);
                $pipe->zrem(self::SORTED_KEY, ...$expiredNicknames);
            });

            if (count($expiredNicknames) < $batchSize) {
                break;
            }
        }

        return array_values(array_unique($avatarsToDelete));
    }
}
