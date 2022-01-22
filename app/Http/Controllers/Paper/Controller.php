<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Models\Paper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public function __construct()
    {
        /** @see EnsureEmailIsVerified */
        $this->middleware('verified');

        /** @see Authenticate */
        $this->middleware('auth');
    }

    protected function findModel(int $id): Model
    {
        return (new Paper)
            ->byUserId(Auth::id())
            ->findOrFail($id);
    }
}
