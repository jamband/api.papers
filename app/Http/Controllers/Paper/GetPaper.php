<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paper;

use App\Http\Resources\PaperResource;

class GetPaper extends Controller
{
    public function __invoke(int $id): PaperResource
    {
        return new PaperResource(
            $this->findModel($id)
        );
    }
}
