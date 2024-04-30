<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
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

    public function __invoke(int $id): PaperResource
    {
        /** @var Paper $paper */
        $paper = $this->paper::query();

        return new PaperResource(
            $paper->byUserId($this->auth->id())
                ->findOrFail($id)
        );
    }
}
