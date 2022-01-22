<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use App\Http\Requests\Paper\UpdateRequest;
use App\Http\Resources\PaperResource;
use App\Models\Paper;

class UpdatePaper extends Controller
{
    public function __invoke(UpdateRequest $request, int $id): PaperResource
    {
        $data = $request->validated();

        /** @var Paper $paper */
        $paper = $this->findModel($id);
        $paper->title = $data['title'];
        $paper->body = $data['body'];
        $paper->save();

        return new PaperResource($paper);
    }
}
