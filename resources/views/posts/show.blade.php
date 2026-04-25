@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
                    @can('update', $post)
                        <div class="flex gap-2">
                            <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Modifier</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Êtes-vous sûr?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
                            </form>
                        </div>
                    @endcan
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Par {{ $post->user->name }} • {{ $post->created_at->format('d/m/Y à H:i') }}
                    @if(!$post->is_published)
                        <span class="ml-2 px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs">Brouillon</span>
                    @endif
                </div>

                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </div>

        <!-- Commentaires -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-4">Commentaires ({{ $post->comments->count() }})</h2>

                @auth
                <form method="POST" action="{{ route('comments.store') }}" class="mb-6">
                    @csrf
                    <input type="hidden" name="commentable_id" value="{{ $post->id }}">
                    <input type="hidden" name="commentable_type" value="App\Models\Post">
                    
                    <textarea name="content" rows="3" placeholder="Ajouter un commentaire..." required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"></textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <button type="submit" class="mt-2 px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                        Commenter
                    </button>
                </form>
                @else
                <p class="mb-6 text-gray-500 dark:text-gray-400">
                    <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Connectez-vous</a> pour commenter.
                </p>
                @endauth

                <div class="space-y-4">
                    @forelse($post->comments as $comment)
                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $comment->user->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Supprimer ce commentaire?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Supprimer</button>
                                    </form>
                                @endcan
                            </div>
                            <p class="mt-2">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">Aucun commentaire pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
