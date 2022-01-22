<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use App\Http\Requests\Paper\CreateRequest;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class CreatePaper extends Controller
{
    public function __invoke(CreateRequest $request): Response
    {
        $data = $request->validated();

        $paper = new Paper;
        $paper->user_id = Auth::id();
        $paper->title = $data['title'];
        $paper->body = $data['body'];
        $paper->save();

        return response(new PaperResource($paper), BaseResponse::HTTP_CREATED)
            ->header('Location', URL::to('/papers/'.$paper->id));
    }
}
