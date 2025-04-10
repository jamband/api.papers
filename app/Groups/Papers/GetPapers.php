<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetPapers extends Controller
{
    public function __construct(
        private readonly Paper $paper,
        private readonly AuthManager $auth,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }
    public function __invoke(): ResourceCollection
    {
        /** @var Paper $paper */
        $paper = $this->paper::query();

        return $paper->byUserId($this->auth->id())
            ->latest()
            ->get()
            ->toResourceCollection(PaperResource::class);
    }
}
