<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\Authenticate;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class DeleteUser extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth:admin');
    }

    public function __invoke(int $id): Response
    {
        User::query()
            ->findOrFail($id)
            ->delete();

        return response()->noContent();
    }
}
