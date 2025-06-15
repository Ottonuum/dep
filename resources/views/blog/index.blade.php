@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Blog Posts</h2>
                        <a href="{{ route('blog.create') }}" class="btn btn-primary">Create New Post</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(count($posts) > 0)
                        @foreach($posts as $post)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <a href="{{ route('blog.show', $post['id']) }}" class="text-decoration-none">
                                            {{ $post['title'] }}
                                        </a>
                                    </h3>
                                    <p class="text-muted">
                                        Posted on {{ \Carbon\Carbon::parse($post['created_at'])->format('F j, Y') }}
                                    </p>
                                    <p class="card-text">
                                        {{ Str::limit($post['content'], 200) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('blog.show', $post['id']) }}" class="btn btn-primary">Read More</a>
                                            <a href="{{ route('blog.show', $post['id']) }}#comments" class="btn btn-info">
                                                Comments 
                                                @if(isset($post['comments']) && count($post['comments']) > 0)
                                                    <span class="badge bg-secondary">{{ count($post['comments']) }}</span>
                                                @endif
                                            </a>
                                        </div>
                                        <div>
                                            <a href="{{ route('blog.edit', $post['id']) }}" class="btn btn-secondary">Edit</a>
                                            <form action="{{ route('blog.destroy', $post['id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">No blog posts found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 