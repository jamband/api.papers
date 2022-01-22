<?php

declare(strict_types=1);

use App\Http\Controllers\Site\{
    CsrfCookie,
    GetUserProfile};
use Illuminate\Support\Facades\Route;

Route::get('/csrf-cookie', CsrfCookie::class);
Route::get('/profile', GetUserProfile::class);
