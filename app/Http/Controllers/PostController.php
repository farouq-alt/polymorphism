<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('user')->where('is_published', true);

        // Recherche par titre
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $posts = $query->latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Post::class);
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        Gate::authorize('create', Post::class);

        // Récupérer les données validées
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Gérer le statut de publication
        $validated['is_published'] = $request->has('is_published') ? true : false;

        // Créer le post
        $post = Post::create($validated);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        Gate::authorize('view', $post);
        
        // Charger les relations
        $post->load(['user', 'comments.user']);
        
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        // Récupérer les données validées
        $validated = $request->validated();

        // Gérer l'upload de la nouvelle image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Gérer le statut de publication
        $validated['is_published'] = $request->has('is_published') ? true : false;

        // Mettre à jour le post
        $post->update($validated);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        // Supprimer l'image si elle existe
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post supprimé avec succès!');
    }
}
