<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
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

/*Route::get('mailable', function () {
    $emailBody = [
        "name"      => "Saqib",
        "url"       => url('/confirmation_email/sdsdsds')
    ];

    return new App\Mail\InviteUserEmail($emailBody);
});*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('confirmation_email/{id}', [AuthController::class, 'confirmation_email']);
Route::get('reset-password/{email}/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [AuthController::class, 'submit_forgot_password'])->name('reset.password.post');

Route::get('/', [AdminAuthController::class, 'index'])->name('login');
Route::post('post-login', [AdminAuthController::class, 'postLogin'])->name('login_post');
Route::post('logout', [AdminAuthController::class, 'admin_logout'])->name('logout');

Route::middleware('is_admin')->group( function () {
    Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/users/{slug?}/{id?}', [AdminAuthController::class, 'users'])->name('users');
    Route::get('/albums/{slug?}/{id?}', [AdminAuthController::class, 'albums'])->name('albums');
    Route::get('/posts/{slug?}/{id?}', [AdminAuthController::class, 'posts'])->name('posts');
});
