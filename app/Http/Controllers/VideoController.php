<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with('user')->where('is_published', true);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $videos = $query->latest()->paginate(10);

        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        Gate::authorize('create', Video::class);
        return view('videos.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Video::class);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'url' => 'required|url',
            'duration' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $video = $request->user()->videos()->create($validated);

        return redirect()->route('videos.show', $video)->with('success', 'Vidéo créée avec succès!');
    }

    public function show(Video $video)
    {
        Gate::authorize('view', $video);
        $video->load(['user', 'comments.user']);
        return view('videos.show', compact('video'));
    }

    public function edit(Video $video)
    {
        Gate::authorize('update', $video);
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        Gate::authorize('update', $video);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'url' => 'required|url',
            'duration' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $video->update($validated);

        return redirect()->route('videos.show', $video)->with('success', 'Vidéo mise à jour!');
    }

    public function destroy(Video $video)
    {
        Gate::authorize('delete', $video);
        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Vidéo supprimée!');
    }
}
