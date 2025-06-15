<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Cache::get('blog_posts', []);
        return view('blog.index', compact('posts'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $posts = Cache::get('blog_posts', []);
        $newPost = [
            'id' => count($posts) + 1,
            'title' => $request->title,
            'content' => $request->content,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $posts[] = $newPost;
        Cache::put('blog_posts', $posts);

        return redirect()->route('blog.index')->with('success', 'Blog post created successfully.');
    }

    public function show($id)
    {
        $posts = Cache::get('blog_posts', []);
        $post = collect($posts)->firstWhere('id', $id);

        if (!$post) {
            abort(404);
        }

        return view('blog.show', compact('post'));
    }

    public function edit($id)
    {
        $posts = Cache::get('blog_posts', []);
        $post = collect($posts)->firstWhere('id', $id);

        if (!$post) {
            abort(404);
        }

        return view('blog.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $posts = Cache::get('blog_posts', []);
        $postIndex = collect($posts)->search(function ($post) use ($id) {
            return $post['id'] == $id;
        });

        if ($postIndex === false) {
            abort(404);
        }

        $posts[$postIndex] = [
            'id' => $id,
            'title' => $request->title,
            'content' => $request->content,
            'created_at' => $posts[$postIndex]['created_at'],
            'updated_at' => now(),
        ];

        Cache::put('blog_posts', $posts);

        return redirect()->route('blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy($id)
    {
        $posts = Cache::get('blog_posts', []);
        $posts = collect($posts)->filter(function ($post) use ($id) {
            return $post['id'] != $id;
        })->values()->all();

        Cache::put('blog_posts', $posts);

        return redirect()->route('blog.index')->with('success', 'Blog post deleted successfully.');
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $posts = Cache::get('blog_posts', []);
        $postIndex = collect($posts)->search(function ($post) use ($postId) {
            return $post['id'] == $postId;
        });

        if ($postIndex === false) {
            abort(404);
        }

        $comments = $posts[$postIndex]['comments'] ?? [];
        $newComment = [
            'id' => count($comments) + 1,
            'content' => $request->content,
            'author' => session('user_email', 'Anonymous'),
            'created_at' => now(),
        ];

        $comments[] = $newComment;
        $posts[$postIndex]['comments'] = $comments;
        Cache::put('blog_posts', $posts);

        return redirect()->route('blog.show', $postId)->with('success', 'Comment added successfully.');
    }

    public function destroyComment($postId, $commentId)
    {
        $posts = Cache::get('blog_posts', []);
        $postIndex = collect($posts)->search(function ($post) use ($postId) {
            return $post['id'] == $postId;
        });

        if ($postIndex === false) {
            abort(404);
        }

        $comments = $posts[$postIndex]['comments'] ?? [];
        $comments = collect($comments)->filter(function ($comment) use ($commentId) {
            return $comment['id'] != $commentId;
        })->values()->all();

        $posts[$postIndex]['comments'] = $comments;
        Cache::put('blog_posts', $posts);

        return redirect()->route('blog.show', $postId)->with('success', 'Comment deleted successfully.');
    }
}
