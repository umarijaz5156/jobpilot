<?php

namespace Modules\Blog\Http\Controllers;

// use App\Models\Category;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Actions\CreatePost;
use Modules\Blog\Actions\DeletePost;
use Modules\Blog\Actions\UpdatePost;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Entities\PostComment;
use Modules\Blog\Http\Requests\PostFormRequest;

class BlogController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['permission:post.view'])->only('index');
        $this->middleware(['permission:post.create'])->only(['create', 'store']);
        $this->middleware(['permission:post.update'])->only(['edit', 'update']);
        $this->middleware(['permission:post.delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the post list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $categories = PostCategory::all();
            $all_posts = Post::all();
            $authors = Post::select('author_id')
                ->with('author:id,name,name')
                ->get()
                ->groupBy('author_id');
            $totalComments = PostComment::count();
            $totalAuthor = $authors->count();
            $totalCategory = $categories->count();
            $totalDraft = $all_posts->where('status', 'draft')->count();
            $totalPublished = $all_posts->where('status', 'published')->count();
            $languages = loadLanguage();

            $query = Post::with('category', 'author:id,name,name')->withCount('comments');
            if ($request->keyword && $request->keyword != null) {
                $query->where('title', 'LIKE', "%$request->keyword%");
            }

            if ($request->category && $request->category != null) {
                $category = $request->category;
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            if ($request->author && $request->author != null) {
                $author = $request->author;
                $query->whereHas('author', function ($q) use ($author) {
                    $q->where('id', $author);
                });
            }

            if ($request->code && $request->code != null) {
                $query->where('locale', $request->code);
            }

            if ($request->status && $request->status != null) {
                $query->where('status', $request->status);
            }
            $blogs = $query
                ->latest()
                ->paginate('15')
                ->withQueryString();

            return view('blog::index', compact('blogs', 'categories', 'authors', 'totalComments', 'totalAuthor', 'totalDraft', 'totalPublished', 'languages'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $categories = PostCategory::all();
            $languages = loadLanguage();

            return view('blog::create', compact('categories', 'languages'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostFormRequest $request)
    {
        try {
            $post = CreatePost::create($request);

            if ($post) {
                flashSuccess(__('post_created_successfully'));

                return redirect()->route('module.blog.index');
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified post.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        try {
            $categories = PostCategory::all();
            $languages = loadLanguage();

            return view('blog::edit', compact('categories', 'post', 'languages'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PostFormRequest $request, Post $post)
    {
        try {
            $post = UpdatePost::update($request, $post);

            if ($post) {
                flashSuccess(__('post_updated_successfully'));

                return redirect()->route('module.blog.index');
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Post $post)
    {
        $post = DeletePost::delete($post);

        if ($post) {
            flashSuccess(__('post_deleted_successfully'));

            return back();
        } else {
            flashError();

            return back();
        }
    }
}
