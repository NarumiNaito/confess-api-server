<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\DeleteRequest;
use App\Http\Requests\Post\IndexRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Comment;
use App\Models\Forgive;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(IndexRequest $request)
    {
        $searchWord = $request->input('search_word');
        $categoryId = $request->input('category_id');

        $query = Post::select('posts.*', 'users.name', 'users.image','categories.category_name')
        ->withCount('comment','forgives')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories','posts.category_id','=','categories.id');

        if ($searchWord) {
        $query->where(function ($q) use ($searchWord) {
            $q->where('users.name', 'like', '%' . $searchWord . '%')
            ->orWhere('posts.content', 'like', '%' . $searchWord . '%');
        });
        }
        
        if ($categoryId) {
        $query->where('posts.category_id', $categoryId);
        }

        $posts = $query
        ->orderBy('updated_at','desc')
        ->paginate(5);
    
        return response()->json($posts);
    }



    public function homeFulfillment(IndexRequest $request)
    {
    
        $categoryId = $request->input('category_id');
    
        $query = Post::select('posts.*', 'users.name', 'users.image', 'categories.category_name')
            ->withCount('comment', 'forgives')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('forgives', 'posts.id', '=', 'forgives.post_id') 
            ->join('users as forgive_users', 'forgives.user_id', '=', 'forgive_users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->groupBy('posts.id') 
            ->distinct(); 
        
        if ($categoryId) {
            $query->where('posts.category_id', $categoryId);
        }
    
        $posts = $query
            ->orderBy('posts.updated_at', 'desc')
            ->paginate(5);
        
        $filteredPosts = $posts->map(function ($post) {
            $post->is_like = $post->forgives->isNotEmpty();
            unset($post->forgives);
            return $post;
        });
    
        $filteredPosts = $filteredPosts->values();
        
        $result = [
            'data' => $filteredPosts,
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ];
    
        return response()->json($result);
    }


    
    public function myIndex(IndexRequest $request)
    {
        $user = Auth::user();
        
        $searchWord = $request->input('search_word');
        $categoryId = $request->input('category_id');

        $query = Post::select('posts.*', 'users.name', 'users.image','categories.category_name')
        ->withCount('comment','forgives')
        ->with(['forgives' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->with(['bookmarks' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories','posts.category_id','=','categories.id');

        if ($searchWord) {
        $query->where(function ($q) use ($searchWord) {
            $q->where('users.name', 'like', '%' . $searchWord . '%')
            ->orWhere('posts.content', 'like', '%' . $searchWord . '%');
        });
        }
        
        if ($categoryId) {
        $query->where('posts.category_id', $categoryId);
        }

        $posts = $query
        ->orderBy('updated_at','desc')
        ->paginate(5);

        $posts->map(function ($post) {
            $post->is_like = $post->forgives->isNotEmpty();
            unset($post->forgives);
            return $post;
        });
    
        $posts->map(function ($post) {
            $post->is_bookmarks = $post->bookmarks->isNotEmpty();
            unset($post->bookmarks);
            return $post;
        });

        return response()->json($posts);
    }

    public function UserIndex($id)
    {
        $user = Auth::user();
        
        $query = Post::where('user_id', $id)
        ->select('posts.*', 'users.name', 'users.image','categories.category_name')
        ->withCount('comment','forgives')
        ->with(['forgives' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->with(['bookmarks' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories','posts.category_id','=','categories.id');

        $posts = $query
        ->orderBy('updated_at','desc')
        ->paginate(5);

        $posts->map(function ($post) {
            $post->is_like = $post->forgives->isNotEmpty();
            unset($post->forgives);
            return $post;
        });
    
        $posts->map(function ($post) {
            $post->is_bookmarks = $post->bookmarks->isNotEmpty();
            unset($post->bookmarks);
            return $post;
        });

        return response()->json($posts);
    }

    public function homeUserIndex($id)
    {
        
        $query = Post::where('user_id', $id)
        ->select('posts.*', 'users.name', 'users.image','categories.category_name')
        ->withCount('comment','forgives')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories','posts.category_id','=','categories.id');

        $posts = $query
        ->orderBy('updated_at','desc')
        ->paginate(5);

        return response()->json($posts);
    }

    public function fulfillment(IndexRequest $request)
    {
        $user = Auth::user();
    
        $categoryId = $request->input('category_id');
    
        $query = Post::select('posts.*', 'users.name', 'users.image', 'categories.category_name')
            ->withCount('comment', 'forgives')
            ->with(['forgives' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->with(['bookmarks' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('forgives', 'posts.id', '=', 'forgives.post_id') 
            ->join('users as forgive_users', 'forgives.user_id', '=', 'forgive_users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->groupBy('posts.id') 
            ->distinct(); 
        
        if ($categoryId) {
            $query->where('posts.category_id', $categoryId);
        }
    
        $posts = $query
            ->orderBy('posts.updated_at', 'desc')
            ->paginate(5);
        
        $filteredPosts = $posts->map(function ($post) {
            $post->is_like = $post->forgives->isNotEmpty();
            unset($post->forgives);
            return $post;
        });

        $posts->map(function ($post) {
            $post->is_bookmarks = $post->bookmarks->isNotEmpty();
            unset($post->bookmarks);
            return $post;
        });
    
        $filteredPosts = $filteredPosts->values();
        
        $result = [
            'data' => $filteredPosts,
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ];
    
        return response()->json($result);
    }


    public function Bookmark(IndexRequest $request)
    {
        $user = Auth::user();
        $categoryId = $request->input('category_id');
    
        $query = Post::select('posts.*', 'users.name', 'users.image', 'categories.category_name')
            ->withCount('comment', 'forgives')
            ->with(['forgives' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->join('bookmarks', 'posts.id', '=', 'bookmarks.post_id') 
            ->where('bookmarks.user_id', $user->id) 
            ->groupBy('posts.id')
            ->distinct();

        if ($categoryId) {
            $query->where('posts.category_id', $categoryId);
        }
    
        $posts = $query
            ->orderBy('posts.updated_at', 'desc')
            ->paginate(5);
    
        $filteredPosts = $posts->map(function ($post) {
            $post->is_like = $post->forgives->isNotEmpty();
            $post->is_bookmarks = true; 
            unset($post->forgives);
            return $post;
        });
    
        $filteredPosts = $filteredPosts->values();
    
        
        $result = [
            'data' => $filteredPosts,
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ];
    
        return response()->json($result);
    }



    // public function fulfillmentMyIndex(IndexRequest $request)
    // {
    //     $user = Auth::user();
        
    //     $categoryId = $request->input('category_id');
    
    //     $query = Post::select('posts.*', 'users.name', 'users.image', 'categories.category_name')
    //         ->withCount('comment', 'forgives')
    //         ->with(['forgives' => function($query) use ($user) {
    //             $query->where('user_id', $user->id);
    //         }])
    //         ->join('users', 'posts.user_id', '=', 'users.id')
    //         ->Join('forgives', 'posts.id', '=', 'forgives.post_id')
    //         ->Join('users as forgive_users', 'forgives.user_id', '=', 'forgive_users.id')
    //         ->join('categories', 'posts.category_id', '=', 'categories.id')
    //         ->where('forgives.user_id', $user->id); 
    
    //     if ($categoryId) {
    //         $query->where('posts.category_id', $categoryId);
    //     }
    
    //     $posts = $query
    //         ->orderBy('updated_at', 'desc')
    //         ->paginate(5);
    
    //     $filteredPosts = $posts->map(function ($post) {
    //         $post->is_like = $post->forgives->isNotEmpty();
    //         unset($post->forgives);
    //         return $post;
    //     });
    
    //     $filteredPosts = $filteredPosts->values();
        
    //     $result = [
    //         'data' => $filteredPosts,
    //         'current_page' => $posts->currentPage(),
    //         'last_page' => $posts->lastPage(),
    //         'per_page' => $posts->perPage(),
    //         'total' => $filteredPosts->count(),
    //     ];
    
    //     return response()->json(
    //         $result,
    //     );
    // }



    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        
        Post::create([
            'user_id' => $user->id,
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
        ]);
        
        return response()->json([
            'message' => '懺悔を登録しました。',
        ]);
    }    



    public function show()
    {
        $user_id = Auth::user()->id;
        
        $query = Post::where('user_id', $user_id)
        ->select('posts.id','user_id','category_id','content','category_name')
        ->withCount('comment','forgives')
        ->join('categories','posts.category_id','=','categories.id')
        ->orderBy('posts.updated_at','desc')
        ->paginate(5);
        
        return response()->json($query);
    }    



    public function update(UpdateRequest $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)
        ->find($request->input('id'));

        if (is_null($post)) {
            return response()->json([
                'message' => '更新対象の懺悔が存在しません。',
            ], 404);
        }

        $post->update([
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'message' => '懺悔を更新しました。',
        ]);
    }




    public function delete(DeleteRequest $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)->find($request->input('id'));
        
        if (is_null($post)) {
            return response()->json([
            'message' => '削除対象の懺悔が存在しません。',
        ], 404);
        }

        // Log::debug(print_r($post));
        
        Comment::where('post_id', '=', $request->id)->delete();
        Forgive::where('post_id', '=', $request->id)->delete();
        $post->delete();
        
        
        return response()->json([
            'message' => '懺悔を削除しました。',
        ]);
    }
}

