@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Pending Comments</h2>
                    <a href="{{ route('admin.blog') }}" class="btn btn-primary">Manage Blogs</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($pendingComments->isEmpty())
                        <p>No pending comments to review.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Author</th>
                                        <th>Content</th>
                                        <th>Blog Post</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingComments as $comment)
                                        <tr>
                                            <td>{{ $comment->author }}</td>
                                            <td>{{ Str::limit($comment->content, 100) }}</td>
                                            <td>
                                                <a href="{{ route('blog.show', $comment->blog) }}">
                                                    {{ $comment->blog->title }}
                                                </a>
                                            </td>
                                            <td>{{ $comment->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 