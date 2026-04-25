<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('create', Comment::class);

        $validated = $request->validate([
            'content' => 'required',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
        ]);

        $comment = $request->user()->comments()->create($validated);

        return back()->with('success', 'Commentaire ajouté!');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);
        $comment->delete();

        return back()->with('success', 'Commentaire supprimé!');
    }
}
