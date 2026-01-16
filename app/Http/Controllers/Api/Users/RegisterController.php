<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisterController
{
    /**
     * UserController constructor
     *
     * @param UserService $service
     */
    public function __construct(
        private readonly UserService $service
    ) {
    }

    /**
     * Register user
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $userDto = $this->service->create(
            nickname: $validated['nickname'],
            avatar: $validated['avatar'],
        );

        return UserResource::make(
            $userDto->toArray()
        )
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
