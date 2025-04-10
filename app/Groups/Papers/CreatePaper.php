<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreatePaper extends Controller
{
    public function __construct(
        private readonly Paper $paper,
        private readonly AuthManager $auth,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
