<?php

use App\Http\Controllers\AlbumsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\InviteFriendsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot_password', [AuthController::class, 'forgotPassword']);



Route::middleware('auth:api')->group( function () {

    Route::post('send_invite', [InviteFriendsController::class, 'process_invites']);
    Route::post('invite_request', [FriendsController::class, 'sendInvite']);
    Route::post('invite_status', [FriendsController::class, 'inviteStatus']);
    Route::post('create_album', [AlbumsController::class, 'store']);
    Route::post('update_album', [AlbumsController::class, 'update']);
    Route::post('get_album_by_id', [AlbumsController::class, 'show']);
    Route::post('delete_album', [AlbumsController::class, 'destroy']);
    Route::post('get_album_friends', [AlbumsController::class, 'getAlbumFriends']);
    Route::post('create_post', [PostsController::class, 'store']);
    Route::post('get_post_by_id', [PostsController::class, 'show']);
    Route::post('post_like', [PostsController::class, 'postLike']);
    Route::post('create_comment', [PostsController::class, 'storeComment']);
    Route::post('update_comment', [PostsController::class, 'updateComment']);
    Route::post('reply_comment', [PostsController::class, 'replyComment']);
    Route::post('delete_comment', [PostsController::class, 'destroyComment']);
    Route::post('post_comments', [PostsController::class, 'postComments']);
    Route::post('update_post', [PostsController::class, 'update']);
    Route::post('remove_tag_from_post', [PostsController::class, 'removeTagFromPost']);
    Route::post('remove_image_from_post', [PostsController::class, 'removeImageFromPost']);
    Route::post('remove_post', [PostsController::class, 'destroy']);
    Route::post('get_posts', [PostsController::class, 'index']);
    Route::post('get_albums', [AlbumsController::class, 'index']);
    Route::post('search_by_name', [UsersController::class, 'searchByName']);
    Route::post('get_my_friends', [FriendsController::class, 'myFriends']);
    Route::post('get_posts_gallery', [PostsController::class, 'getPostsGallery']);
    Route::post('get_top_tags', [PostsController::class, 'getTopTags']);
    Route::post('get_album_gallery', [PostsController::class, 'getAlbumGallery']);
    Route::post('get_album_tags', [PostsController::class, 'getAlbumTags']);
    Route::post('update_profile', [AuthController::class, 'update_profile']);
    Route::post('change_password', [AuthController::class, 'changePassword']);
    Route::post('get_notification', [NotificationsController::class, 'index']);
    Route::post('update_notification_status', [NotificationsController::class, 'updateStatus']);
});
