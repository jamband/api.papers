<?php

declare(strict_types=1);

use App\Http\Controllers\Paper\{
    CreatePaper,
    DeletePaper,
    GetPaper,
    GetPapers,
    UpdatePaper};
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[\d]+');

Route::get('', GetPapers::class);
Route::get('{id}', GetPaper::class);
Route::post('', CreatePaper::class);
Route::put('{id}', UpdatePaper::class);
Route::delete('{id}', DeletePaper::class);
