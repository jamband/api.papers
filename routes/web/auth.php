<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\{
    ConfirmedPassword,
    ConfirmPassword,
    DeleteAccount,
    EmailVerificationNotification,
    ForgotPassword,
    GetUser,
    Login,
    Logout,
    Register,
    ResetPassword,
    VerifyEmail};
use Illuminate\Support\Facades\Route;

Route::get('/user', GetUser::class);

Route::post('/login', Login::class);
Route::post('/logout', Logout::class);

Route::post('/register', Register::class);
Route::post('/delete-account', DeleteAccount::class);

Route::post('/forgot-password', ForgotPassword::class)
    ->name('password.forgot');

Route::post('/reset-password', ResetPassword::class)
    ->name('password.update');

Route::get('/email/verify/{id}/{hash}', VerifyEmail::class)
    ->name('verification.verify');

Route::post('/email/verification-notification', EmailVerificationNotification::class)
    ->name('verification.send');

Route::post('/confirm-password', ConfirmPassword::class)
    ->name('password.confirm');

Route::get('/confirmed-password',  ConfirmedPassword::class);
