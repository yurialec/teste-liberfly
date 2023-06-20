<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *      tags={"/api/blog"},
     *      summary="Display a list of the resource",
     *      description="get all blogs on database",
     *      path="/blog",
     *      security={"bearerAuth": {}},
     *      @OA\Response(
     *          response="280", description="List of blogs"
     *      )
     * )
     * @return object
     */
    public function index()
    {
        $blogs = Blog::all();
        return response()->json([
            'status' => 'success',
            'blogs' => $blogs,
        ]);
    }

    /**
     * Insert this code on method post() on
     * \App\Http\Controllers\UsersController.php
     */
    /**
     * Storing a new resource.
     *
     * @OA\Post(
     *   path="/api/blog/create",
     *   tags={"/api/blog/create"},
     *   @OA\Response(
     *     response=200,
     *     description="Response Successful",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="message",
     *           type="string",
     *           example="Successful action!"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Response Error"
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="title",
     *           type="string",
     *           description="The title of the blog.",
     *           example="Lista dos 10 melhores carros"
     *         ),
     *         @OA\Property(
     *           property="content",
     *           type="text",
     *           description="The content of the blog.",
     *           example="Numero 1..."
     *         ),
     *       )
     *     )
     *   )
     * )
     *
     * @return array
     */
    public function create(Request $request)
    {
        $user = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', 'min:6'],
            'content' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                409
            ]);
        }

        try {
            $blog = Blog::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => $user,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Blog created successfully',
                'blog' => $blog,
            ]);
        } catch (Exception $err) {
            // dd($err);
            return response()->json([
                'status' => 'Error',
                'message' => "Erro ao cadastrar. Tente Novamente.",
            ]);
        }
    }

    /**
     * Insert this code on method post() on
     * \App\Http\Controllers\UsersController.php
     */
    /**
     * Storing a new resource.
     *
     * @OA\Get(
     *   path="/api/blog/{blog_id}",
     *   tags={"/api/blog/{blog_id}"},
     *   @OA\Response(
     *     response=200,
     *     description="Response Successful",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="message",
     *           type="string",
     *           example="Successful action!"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Response Error"
     *   ),
     *   )
     * )
     *
     * @return array
     */
    public function show($blog_id)
    {
        $blog = Blog::where('id', '=', $blog_id)->get();

        return response()->json([
            'status' => 'success',
            'blog' => $blog,
        ]);
    }

    /**
     * Search for a blog with name or content
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                409
            ]);
        }

        $blog = Blog::where('title', 'LIKE', "%{$request->search}%")
            ->orWhere('content', 'LIKE', "%{$request->search}%")
            ->get();

        if (!empty($blog[0])) {
            return response()->json([
                'status' => 'success',
                'blog' => $blog,
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Nenhum resultado encontrado',
            ]);
        }
    }

    /**
     * Insert this code on method post() on
     * \App\Http\Controllers\UsersController.php
     */
    /**
     * Storing a new resource.
     *
     * @OA\Put(
     *   path="/api/blog/update/{blog_id}",
     *   tags={"/api/blog/update/{blog_id}"},
     *   @OA\Response(
     *     response=200,
     *     description="Response Successful",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="message",
     *           type="string",
     *           example="Successful action!"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Response Error"
     *   ),
     *   )
     * )
     *
     * @return array
     */
    public function update(Request $request, $blog_id)
    {
        $findBlog = Blog::where('id', '=', $blog_id)->first();
        $user = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', 'min:6'],
            'content' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                409
            ]);
        }

        if ($findBlog->user_id == $user) {

            try {

                $data = $request->all();
                $findBlog->update($data);

                return response()->json([
                    'status' => 'success',
                    'blog' => $findBlog,
                ]);
            } catch (Exception $err) {
                // dd($err);
                return response()->json([
                    'status' => 'Error',
                    'message' => "Erro na atualização. Tente Novamente.",
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => "Sem autorização para editar.",
            ]);
        }
    }

    /**
     * Deleting a specific resource
     *
     * @OA\Delete(
     *   path="/api/blog/delete/{blog_id}",
     *   tags={"/api/blog/delete/{blog_id}"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Identification of User",
     *     example=1,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Response Successful",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="message",
     *           type="string",
     *           example="Successful action!"
     *         ),
     *         @OA\Property(
     *           property="data",
     *           type="boolean",
     *           example=true
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Response Error"
     *   )
     * )
     *
     * @param  int $id
     * @return array
     */
    public function delete($blog_id)
    {
        $findBlog = Blog::where('id', '=', $blog_id)->first();
        $user = Auth::user()->id;

        if ($findBlog->user_id == $user) {

            try {
                $findBlog->delete();

                return response()->json([
                    'status' => 'success',
                ]);
            } catch (Exception $err) {
                // dd($err);
                return response()->json([
                    'status' => 'Error',
                    'message' => "Erro na atualização. Tente Novamente.",
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => "Sem autorização para editar.",
            ]);
        }
    }
}
