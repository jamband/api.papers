<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use App\Http\Resources\PaperResource;
use App\Models\Paper;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class GetPapers extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return PaperResource::collection(
            (new Paper)
                ->byUserId(Auth::id())
                ->latest()
                ->get()
        );
    }
}
