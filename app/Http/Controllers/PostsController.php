<?php

namespace App\Http\Controllers;

use App\Entities\Album;
use App\Entities\AlbumFriend;
use App\Http\Resources\Post;
use App\Repositories\AlbumPostRepositoryEloquent;
use App\Repositories\PostImageRepositoryEloquent;
use App\Repositories\PostLikeRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\PostRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravelista\Comments\Comment;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PostsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PostsController extends BaseController
{
    /**
     * @var PostRepository
     */
    protected $repository;
    /**
     * @var AlbumPostRepositoryEloquent
     */
    private $albumPostRepositoryEloquent;
    /**
     * @var PostImageRepositoryEloquent
     */
    private $postImageRepositoryEloquent;
    /**
     * @var PostLikeRepositoryEloquent
     */
    private $postLikeRepositoryEloquent;

    /**
     * PostsController constructor.
     *
     * @param PostRepository $repository
     * @param AlbumPostRepositoryEloquent $albumPostRepositoryEloquent
     * @param PostImageRepositoryEloquent $postImageRepositoryEloquent
     * @param PostLikeRepositoryEloquent $postLikeRepositoryEloquent
     */
    public function __construct(
        PostRepository $repository,
        AlbumPostRepositoryEloquent $albumPostRepositoryEloquent,
        PostImageRepositoryEloquent $postImageRepositoryEloquent,
        PostLikeRepositoryEloquent $postLikeRepositoryEloquent
    )
    {
        $this->repository = $repository;
        $this->albumPostRepositoryEloquent = $albumPostRepositoryEloquent;
        $this->postImageRepositoryEloquent = $postImageRepositoryEloquent;
        $this->postLikeRepositoryEloquent = $postLikeRepositoryEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $userAlbums = Album::where("created_by", \auth()->user()->id)->get();
        $userAlbums = ($userAlbums->count() > 0) ? $userAlbums->pluck("id")->toArray() : [];

        $userAddedAlbums = AlbumFriend::where("user_id", \auth()->user()->id)->get();
        $userAddedAlbums = ($userAddedAlbums->count() > 0) ? $userAddedAlbums->pluck("album_id")->toArray() : [];

        $albumIds = array_merge($userAlbums, $userAddedAlbums);

        $posts = \App\Entities\Post::select("posts.*")->join('album_posts', 'post_id', '=', 'posts.id')
            ->whereIn("album_id", $albumIds)
            ->orderBy('posts.id', 'DESC')
            ->get();

        return $this->sendResponse(Post::collection($posts),"");
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
            'caption' => 'required',
            'cover_image' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Upload Image
        $images = [];
        foreach ($request->cover_image as $key => $image) {
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '.' . 'png';
            File::put(public_path() . '/img/' . $imageName, base64_decode($image));
            array_push($images,url('/img/'.$imageName));
        }

        $input = [
            "caption"          => $request->input('caption'),
            "created_by"    => $request->user()->id
        ];

        $post = $this->repository->create($input);

        // Store Images
        $this->postImageRepositoryEloquent->store($post->id, $images);

        if($request->has('albums')) {
            $this->albumPostRepositoryEloquent->store($post->id, $request->input('albums'));
        }

        if($request->has('tags')) {
            $post->attachTags($request->input("tags"));
        }

        return $this->sendResponse(Post::make($post), "Post Created Successfully!");
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
            'post_id' => 'required|integer|exists:posts,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $album = $this->repository->findWhere([
            "id"            => $request->input("post_id"),
            "created_by"    => $request->user()->id
        ])->first();

        return $this->sendResponse(Post::make($album), "");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'required|string',
            'id' => 'required|int'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->has('cover_image')) {
            // Upload Image
            $images = [];
            foreach ($request->cover_image as $key => $image) {
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time() . '.' . 'png';
                File::put(public_path() . '/img/' . $imageName, base64_decode($image));
                array_push($images, url('/img/' . $imageName));
            }

            // Store Images
            $this->postImageRepositoryEloquent->store($request->id, $images, true);
        }

        $input = [
            "caption"          => $request->input('caption'),
            "created_by"    => $request->user()->id
        ];

        $post = $this->repository->update($input,$request->id);

        if($request->has('albums')) {
            $this->albumPostRepositoryEloquent->store($request->id, $request->input('albums'));
        }

        if($request->has('tags')) {
            $post->attachTags($request->input("tags"));
        }

        return $this->sendResponse(Post::make($post), "Post Created Successfully!");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $this->repository->delete($request->post_id);

        return $this->sendResponse([],"Post Deleted");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function postLike(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $check = $this->postLikeRepositoryEloquent->findWhere([
            "post_id"   => $request->input("post_id"),
            "user_id"   => auth()->user()->id
        ]);

        if($check->count() > 0){
            $this->postLikeRepositoryEloquent->deleteWhere([
                "post_id"   => $request->input("post_id"),
                "user_id"   => auth()->user()->id
            ]);
        }else {
            $this->postLikeRepositoryEloquent->store($request);
        }

        return $this->sendResponse([],"Post Likes Updated");
    }

    /**
     * Creates a new comment for given model.
     * @param Request $request
     * @return JsonResponse
     */
    public function storeComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commentable_id'    => 'required|integer|exists:posts,id',
            'message'           => 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $model = \App\Entities\Post::findOrFail($request->commentable_id);

        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        $comment->commenter()->associate(Auth::user());

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->save();

        return $this->sendResponse($comment, "Comment Added");
    }

    /**
     * Updates the message of the comment.
     * @param Request $request
     * @return JsonResponse
     */
    public function updateComment(Request $request)
    {
        $comment = new Comment();

        $validator = Validator::make($request->all(), [
            'message'           => 'required|string',
            'comment_id'    => 'required|integer|exists:comments,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $getComment = $comment->where("id", $request->comment_id)->first();

        $getComment->update([
            'comment' => $request->message
        ]);

        return $this->sendResponse($getComment, "Comment Updated");
    }

    /**
     * Deletes a comment.
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id'    => 'required|integer|exists:comments,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $comment = new Comment();
        $comment = $comment->where("id", $request->comment_id);

        if (Config::get('comments.soft_deletes') == true) {
            $comment->delete();
        }
        else {
            $comment->forceDelete();
        }

        return $this->sendResponse($comment, "Comment Deleted");
    }

    /**
     * Creates a reply "comment" to a comment.
     * @param Request $request
     * @return JsonResponse
     */
    public function replyComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id'    => 'required|integer|exists:comments,id',
            'message'       => 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $comment = new Comment();
        $comment = $comment->where("id", $request->comment_id)->first();

        $commentClass = Config::get('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = !Config::get('comments.approval_required');
        $reply->save();

        return $this->sendResponse($comment, "Comment Replied");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postComments(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id'    => 'required|integer|exists:posts,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $post = \App\Entities\Post::where("id", $request->post_id)->first();

        return $this->sendResponse($post->comments, "");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeTagFromPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id'   => 'required|integer|exists:posts,id',
            'tag'       => 'required|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $post = $this->repository->findWhere([
            "id"            => $request->input("post_id")
        ])->first();

        $post->detachTags($request->tag);

        return $this->sendResponse(Post::make($post), "");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeImageFromPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id'   => 'required|integer|exists:posts,id',
            'image_id'       => 'required|integer|exists:post_images,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $post = $this->repository->findWhere([
            "id"            => $request->input("post_id")
        ])->first();

        $this->postImageRepositoryEloquent->delete($request->image_id);

        return $this->sendResponse(Post::make($post), "");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPostsGallery(Request $request){
        $posts = $this->repository->findWhere([
            "created_by"    => Auth::user()->id
        ]);

        $postIds = $posts->pluck("id")->toArray();
        $images = $this->postImageRepositoryEloquent->findWhereIn("post_id", $postIds);

        return $this->sendResponse($images, "");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTopTags(Request $request){
        $postResource = new \App\Entities\Post();
        $resutls = $postResource->scopePopularTags();
        return $this->sendResponse($resutls, "");
    }

    public function getAlbumGallery(Request $request){
        $posts = $this->albumPostRepositoryEloquent->findWhere([
            "album_id"    => $request->input("album_id")
        ]);

        $postIds = $posts->pluck("post_id")->toArray();
        $images = $this->postImageRepositoryEloquent->findWhereIn("post_id", $postIds);

        return $this->sendResponse($images, "");
    }

    public function getAlbumTags(Request $request){
        $posts = $this->albumPostRepositoryEloquent->findWhere([
            "album_id"    => $request->input("album_id")
        ]);

        $postIds = $posts->pluck("post_id")->toArray();
        $postResource = new \App\Entities\Post();
        $resutls = $postResource->postsTags($postIds);
        return $this->sendResponse($resutls, "");
    }
}
