<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('mailable', function () {
    $emailBody = [
        "name"      => "Saqib",
        "url"       => url('/confirmation_email/sdsdsds')
    ];

    return new App\Mail\InviteUserEmail($emailBody);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('confirmation_email/{id}', [\App\Http\Controllers\AuthController::class, 'confirmation_email']);
Route::get('reset-password/{email}/{token}', [\App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [\App\Http\Controllers\AuthController::class, 'submit_forgot_password'])->name('reset.password.post');
