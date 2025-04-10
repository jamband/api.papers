<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class GetPaper extends Controller
{
    public function __construct(
        private readonly Paper $paper,
        private readonly AuthManager $auth,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResource
    {
        /** @var Paper $paper */
        $paper = $this->paper::query();

        return $paper->byUserId($this->auth->id())
            ->findOrFail($id)->toResource(PaperResource::class);
    }
}
