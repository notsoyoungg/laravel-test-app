<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\UserService;
use Illuminate\View\View;

class IndexController
{
    /**
     * UserController constructor
     */
    public function __construct(
        private readonly UserService $service
    ) {
    }

    /**
     * Get all users
     *
     * @return View
     */
    public function all(): View
    {
        $users = $this->service->all();

        return view('users.index', compact('users'));
    }
}
