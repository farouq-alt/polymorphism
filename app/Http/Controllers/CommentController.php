<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        Gate::authorize('create', Comment::class);

        // Récupérer les données validées
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        // Gérer l'upload de l'image (optionnel pour les commentaires)
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('comments', 'public');
        }

        // Créer le commentaire
        $comment = Comment::create($validated);

        return back()->with('success', 'Commentaire ajouté avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        // Supprimer l'image si elle existe
        if ($comment->image) {
            Storage::disk('public')->delete($comment->image);
        }

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé avec succès!');
    }
}
