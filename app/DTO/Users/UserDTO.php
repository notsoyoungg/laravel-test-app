<?php

namespace App\DTO\Users;

final class UserDTO
{
    /**
     * Nickname.
     *
     * @var string
     */
    public string $nickname;

    /**
     * Avatar.
     *
     * @var string
     */
    public string $avatar;

    /**
     * Created at.
     *
     * @var string
     */
    public string $createdAt;

    /**
     * UpdateDTO constructor.
     *
     * @param string $nickname
     * @param string $avatar
     * @param string $createdAt
     */
    public function __construct(string $nickname, string $avatar, string $createdAt)
    {
        $this->nickname  = $nickname;
        $this->avatar    = $avatar;
        $this->createdAt = $createdAt;
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'nickname'   => $this->nickname,
            'avatar'     => $this->avatar,
            'created_at' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nickname'],
            $data['avatar'],
            $data['created_at']
        );
    }
}
