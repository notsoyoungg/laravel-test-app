<?php

namespace App\Rules\Users;

use App\Contracts\Repositories\Users\UserRepositoryContract;
use App\Repositories\Users\UserRepository;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueNicknameRule implements ValidationRule
{
    /**
     * UserRepository instance
     *
     * @var UserRepositoryContract
     */
    private UserRepositoryContract $repository;

    /**
     * UniqueNicknameRule constructor
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->repository = app()->make(UserRepository::class);
    }

    /**
     * Validate nickname
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->repository->exists($value)) {
            $fail("Nickname $value already exists");
        }
    }
}
