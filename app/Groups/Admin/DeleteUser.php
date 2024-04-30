<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use App\Groups\Users\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteUser extends Controller
{
    public function __construct(
        private readonly User $user,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('auth:admin');
    }

    public function __invoke(int $id): Response
    {
        $this->user::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
