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
     * List all blogs registered
     *
     * @return void
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
     * Create a new blog
     *
     * @param Request $request
     * @return void
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
     * Show a especific blog
     *
     * @param [type] $blog_id
     * @return void
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
     * Search blog per id
     * Verify the owner
     * update blog
     *
     * @param Request $request
     * @param [type] $blog_id
     * @return void
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
     * Search blog per id
     * Verify the owner
     * delete blog
     *
     * @param [type] $blog_id
     * @return void
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
