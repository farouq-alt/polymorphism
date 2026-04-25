@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('partials.nav')

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Tous les Posts</h2>
                    
                    <!-- Formulaire de recherche -->
                    <form method="GET" action="{{ route('posts.index') }}" class="flex gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Rechercher un post..." 
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                        >
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            Rechercher
                        </button>
                        @if(request('search'))
                            <a 
                                href="{{ route('posts.index') }}" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 flex items-center"
                            >
                                Réinitialiser
                            </a>
                        @endif
                    </form>
                </div>

                @if(request('search'))
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Résultats pour : <strong>{{ request('search') }}</strong> ({{ $posts->total() }} post(s) trouvé(s))
                    </div>
                @endif

                <!-- Liste des posts -->
                <div class="space-y-6">
                    @forelse($posts as $post)
                        <article class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="md:flex">
                                <!-- Image du post -->
                                @if($post->image)
                                    <div class="md:w-1/3">
                                        <img 
                                            src="{{ asset('storage/' . $post->image) }}" 
                                            alt="{{ $post->title }}"
                                            class="w-full h-48 md:h-full object-cover"
                                        >
                                    </div>
                                @endif

                                <!-- Contenu du post -->
                                <div class="p-6 {{ $post->image ? 'md:w-2/3' : 'w-full' }}">
                                    <h3 class="text-xl font-semibold mb-2">
                                        <a 
                                            href="{{ route('posts.show', $post) }}" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:underline"
                                        >
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        {{ Str::limit($post->content, 200) }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $post->user->name }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $post->created_at->diffForHumans() }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $post->comments->count() }} commentaire(s)
                                            </span>
                                        </div>
                                        
                                        <a 
                                            href="{{ route('posts.show', $post) }}" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm"
                                        >
                                            Lire la suite →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun post trouvé</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(request('search'))
                                    Aucun résultat pour votre recherche.
                                @else
                                    Commencez par créer un nouveau post.
                                @endif
                            </p>
                            @auth
                                <div class="mt-6">
                                    <a 
                                        href="{{ route('posts.create') }}" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Créer un post
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
