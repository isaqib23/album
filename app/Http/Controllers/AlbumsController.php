<?php

namespace App\Http\Controllers;

use App\Entities\AlbumFriend;
use App\Http\Resources\Album;
use App\Repositories\AlbumFriendRepositoryEloquent;
use App\Repositories\AlbumPostRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\AlbumRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class AlbumsController.
 *
 * @package namespace App\Http\Controllers;
 */
class AlbumsController extends BaseController
{
    /**
     * @var AlbumRepository
     */
    protected $repository;
    /**
     * @var AlbumFriendRepositoryEloquent
     */
    private $albumFriendRepositoryEloquent;
    /**
     * @var AlbumPostRepositoryEloquent
     */
    private $albumPostRepositoryEloquent;

    /**
     * AlbumsController constructor.
     *
     * @param AlbumRepository $repository
     * @param AlbumFriendRepositoryEloquent $albumFriendRepositoryEloquent
     * @param AlbumPostRepositoryEloquent $albumPostRepositoryEloquent
     */
    public function __construct(
        AlbumRepository $repository,
        AlbumFriendRepositoryEloquent $albumFriendRepositoryEloquent,
        AlbumPostRepositoryEloquent $albumPostRepositoryEloquent
    )
    {
        $this->repository = $repository;
        $this->albumFriendRepositoryEloquent = $albumFriendRepositoryEloquent;
        $this->albumPostRepositoryEloquent = $albumPostRepositoryEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $userAlbums = \App\Entities\Album::where("created_by", \auth()->user()->id)->get();
        $userAlbums = ($userAlbums->count() > 0) ? $userAlbums->pluck("id")->toArray() : [];

        $userAddedAlbums = AlbumFriend::where("user_id", \auth()->user()->id)->get();
        $userAddedAlbums = ($userAddedAlbums->count() > 0) ? $userAddedAlbums->pluck("album_id")->toArray() : [];

        $albumIds = array_merge($userAlbums, $userAddedAlbums);

        $albums = $this->repository->orderBy("id","desc")->findWhereIn("id",$albumIds);

        return $this->sendResponse(Album::collection($albums), 'Albums retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'cover_image' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Upload Image
        $image = $request->cover_image;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time().'.'.'png';
        File::put(public_path().'/img/'. $imageName, base64_decode($image));

        $input = [
            "name"          => $request->input('name'),
            "cover_image"   => url('/img/'.$imageName),
            "created_by"    => $request->user()->id
            ];

        $album = $this->repository->create($input);

        if($request->has('friends')) {
            // Add Album Friends
            $this->albumFriendRepositoryEloquent->store($album->id, $request->input('friends'));
        }

        if($request->has('post_ids')) {
            $this->albumPostRepositoryEloquent->storePostsToAlbum($album->id, $request->input('post_ids'));
        }

        return $this->sendResponse(Album::make($album), "Album Created Successfully!");
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required|integer|exists:albums,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $album = $this->repository->findWhere([
            "id"            => $request->input("album_id"),
            "created_by"    => $request->user()->id
        ])->first();

        return $this->sendResponse(Album::make($album), "Album Data");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     *
     * @return JsonResponse
     *
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required|integer|exists:albums,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = [
            "name"          => $request->input('name')
        ];

        if($request->has("cover_image")) {
            // Upload Image
            $image = $request->cover_image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '.' . 'png';
            File::put(public_path() . '/img/' . $imageName, base64_decode($image));
            $input["cover_image"] = url('/img/'.$imageName);
        }

        $album = $this->repository->update($input,$request->id);

        if($request->has('friends')) {
            // Add Album Friends
            $this->albumFriendRepositoryEloquent->store($album->id, $request->input('friends'));
        }

        return $this->sendResponse(Album::make($album), "Album Created Successfully!");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return $this->sendResponse([],"Album Deleted");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAlbumFriends(Request $request){
        $validator = Validator::make($request->all(), [
            'album_id' => 'required|integer|exists:albums,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $friendIds = AlbumFriend::where("album_id",$request->album_id)->get();

        $friends = \App\Models\User::whereIn("id",$friendIds->pluck("user_id"))->get();

        return $this->sendResponse($friends,"");
    }
}
