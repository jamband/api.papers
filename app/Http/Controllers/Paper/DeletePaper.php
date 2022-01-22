<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use Illuminate\Http\Response;

class DeletePaper extends Controller
{
    public function __invoke(int $id): Response
    {
        $this->findModel($id)->delete();

        return response()->noContent();
    }
}
