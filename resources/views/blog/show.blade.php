@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">{{ $post['title'] }}</h2>
                        <div>
                            <a href="{{ route('blog.edit', $post['id']) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('blog.destroy', $post['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <p class="text-muted">Posted on {{ \Carbon\Carbon::parse($post['created_at'])->format('F j, Y') }}</p>
                    </div>

                    <div class="content mb-5">
                        {!! nl2br(e($post['content'])) !!}
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section mt-5" id="comments">
                        <h3>Comments</h3>
                        
                        @if(isset($post['comments']) && count($post['comments']) > 0)
                            @foreach($post['comments'] as $comment)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title">
                                                    {{ $comment['author'] }}
                                                    <small class="text-muted">
                                                        ({{ session('role', 'User') }})
                                                    </small>
                                                </h5>
                                                <p class="card-text">{{ $comment['content'] }}</p>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('F j, Y g:i A') }}
                                                </small>
                                            </div>
                                            <form action="{{ route('blog.comments.destroy', [$post['id'], $comment['id']]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this comment?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        @endif

                        <!-- Comment Form -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <h4>Leave a Comment</h4>
                                <p class="text-muted mb-3">
                                    Commenting as: <strong>{{ session('user_email', 'Anonymous') }}</strong>
                                    ({{ session('role', 'User') }})
                                </p>
                                <form action="{{ route('blog.comments.store', $post['id']) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Comment</label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                            id="content" name="content" rows="3" required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('blog.index') }}" class="btn btn-secondary">Back to Blog</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 