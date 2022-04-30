<?php

namespace App\Http\Controllers;

use App\Entities\Album;
use App\Entities\AlbumFriend;
use App\Entities\Post;
use App\Models\User;
use App\Repositories\AlbumRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * @var AlbumRepository
     */
    private $repository;

    /**
     * AdminAuthController constructor.
     * @param AlbumRepository $repository
     */
    public function __construct(
        AlbumRepository $repository
    ){
        $this->repository = $repository;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')
            ->with('error','Email-Address And Password Are Wrong.');
    }

    /**
     * @return Application|Factory|View
     */
    public function dashboard()
    {
        if(Auth::check()){
            $data["users"] = User::where("type","<>","admin")->count();
            $data["albums"] = Album::count();
            $data["posts"] = Post::count();
            return view('home',$data);
        }

        return redirect("login")->withSuccess('Opps! You do not have access');
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function admin_logout() {
        \Illuminate\Support\Facades\Session::flush();
        Auth::logout();

        return Redirect('/');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function users(Request $request){
        if($request->segment(2) && $request->segment(3)){
            $album_id = $request->segment(3);

            $friendIds = AlbumFriend::where("album_id",$album_id)->get();

            $users =User::whereIn("id",$friendIds->pluck("user_id"))->get();
            $data["users"] = \App\Http\Resources\User::collection($users);
            return view('users',$data);
        }
        $users = User::where("type","<>","admin")->get();
        $data["users"] = \App\Http\Resources\User::collection($users);
        return view('users',$data);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function albums(Request $request){
        if($request->segment(2) && $request->segment(3)){
            $id = $request->segment(3);

            $userAlbums = \App\Entities\Album::where("created_by", $id)->get();
            $userAlbums = ($userAlbums->count() > 0) ? $userAlbums->pluck("id")->toArray() : [];

            $userAddedAlbums = AlbumFriend::where([
                "user_id"   => $id,
                "status"    => "accepted"
            ])->get();
            $userAddedAlbums = ($userAddedAlbums->count() > 0) ? $userAddedAlbums->pluck("album_id")->toArray() : [];

            $albumIds = array_merge($userAlbums, $userAddedAlbums);

            $albums = $this->repository->orderBy("id","desc")->findWhereIn("id",$albumIds);
            $data["albums"] = \App\Http\Resources\Album::collection($albums);
            return view('albums',json_decode(json_encode($data["albums"])));
        }


        $albums = Album::get();
        $data["albums"] = \App\Http\Resources\Album::collection($albums);
        return view('albums',json_decode(json_encode($data["albums"])));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function posts(Request $request){
        if($request->segment(2) && $request->segment(3)){
            $id = $request->segment(3);
            if($request->segment(2) == 'user'){
                $userAlbums = Album::where("created_by", $id)->get();
                $userAlbums = ($userAlbums->count() > 0) ? $userAlbums->pluck("id")->toArray() : [];

                $userAddedAlbums = AlbumFriend::where([
                    "user_id"   => $id,
                    "status"    => "accepted"
                ])->get();
                $userAddedAlbums = ($userAddedAlbums->count() > 0) ? $userAddedAlbums->pluck("album_id")->toArray() : [];

                $albumIds = array_merge($userAlbums, $userAddedAlbums);

                $posts = \App\Entities\Post::select("posts.*")->join('album_posts', 'post_id', '=', 'posts.id')
                    ->whereIn("album_id", $albumIds)
                    ->orderBy('posts.id', 'DESC')
                    ->get();
                $data["posts"] = \App\Http\Resources\Post::collection($posts);
                return view('posts',json_decode(json_encode($data["posts"])));
            }

            if($request->segment(2) == 'album'){
                $posts = \App\Entities\Post::select("posts.*")->join('album_posts', 'post_id', '=', 'posts.id')
                    ->whereIn("album_id", [$id])
                    ->orderBy('posts.id', 'DESC')
                    ->get();
                $data["posts"] = \App\Http\Resources\Post::collection($posts);
                return view('posts',json_decode(json_encode($data["posts"])));
            }
        }
        $posts = Post::get();
        $data["posts"] = \App\Http\Resources\Post::collection($posts);
        return view('posts',json_decode(json_encode($data["posts"])));
    }
}
