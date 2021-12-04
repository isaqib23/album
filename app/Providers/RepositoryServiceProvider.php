<?php

namespace App\Providers;

use App\Repositories\InviteFriendRepository;
use App\Repositories\InviteFriendRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AlbumRepository::class, \App\Repositories\AlbumRepositoryEloquent::class);
        $this->app->bind(InviteFriendRepository::class, InviteFriendRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FriendRepository::class, \App\Repositories\FriendRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AlbumFriendRepository::class, \App\Repositories\AlbumFriendRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PostRepository::class, \App\Repositories\PostRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AlbumPostRepository::class, \App\Repositories\AlbumPostRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PostImageRepository::class, \App\Repositories\PostImageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PostLikeRepository::class, \App\Repositories\PostLikeRepositoryEloquent::class);
        //:end-bindings:
    }
}
