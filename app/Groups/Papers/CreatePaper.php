<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreatePaper
{
    public function __construct(
        private Paper $paper,
        private AuthManager $auth,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreatePaperRequest $request): Response
    {
        $data = $request->validated();

        $this->paper->user_id = $this->auth->id();
        $this->paper->title = $data['title'];
        $this->paper->body = $data['body'];
        $this->paper->save();

        return $this->response->make($this->paper->toResource(PaperResource::class), 201)
            ->header('Location', $this->url->to('/papers/'.$this->paper->id));
    }
}
