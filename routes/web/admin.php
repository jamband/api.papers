<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\{
    DeleteUser,
    GetUsers,
    Login,
    Logout};
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[\d]+');

Route::post('login', Login::class);
Route::post('logout', Logout::class);

Route::get('users', GetUsers::class);
Route::delete('users/{id}', DeleteUser::class);
