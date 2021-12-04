<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\AlbumPostCreateRequest;
use App\Http\Requests\AlbumPostUpdateRequest;
use App\Repositories\AlbumPostRepository;
use App\Validators\AlbumPostValidator;

/**
 * Class AlbumPostsController.
 *
 * @package namespace App\Http\Controllers;
 */
class AlbumPostsController extends Controller
{
    /**
     * @var AlbumPostRepository
     */
    protected $repository;

    /**
     * @var AlbumPostValidator
     */
    protected $validator;

    /**
     * AlbumPostsController constructor.
     *
     * @param AlbumPostRepository $repository
     * @param AlbumPostValidator $validator
     */
    public function __construct(AlbumPostRepository $repository, AlbumPostValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $albumPosts = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $albumPosts,
            ]);
        }

        return view('albumPosts.index', compact('albumPosts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AlbumPostCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(AlbumPostCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $albumPost = $this->repository->create($request->all());

            $response = [
                'message' => 'AlbumPost created.',
                'data'    => $albumPost->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $albumPost = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $albumPost,
            ]);
        }

        return view('albumPosts.show', compact('albumPost'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $albumPost = $this->repository->find($id);

        return view('albumPosts.edit', compact('albumPost'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AlbumPostUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(AlbumPostUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $albumPost = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'AlbumPost updated.',
                'data'    => $albumPost->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'AlbumPost deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'AlbumPost deleted.');
    }
}
