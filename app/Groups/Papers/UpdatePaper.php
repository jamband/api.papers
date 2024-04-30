<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Routing\Controller;

class UpdatePaper extends Controller
{
    public function __construct(
        private readonly Paper $paper,
        private readonly AuthManager $auth,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdatePaperRequest $request, int $id): PaperResource
    {
        $data = $request->validated();

        /** @var Paper $query */
        $query = $this->paper::query();

        $paper = $query->byUserId($this->auth->id())
            ->findOrFail($id);

        $paper->title = $data['title'];
        $paper->body = $data['body'];
        $paper->save();

        return new PaperResource($paper);
    }
}
