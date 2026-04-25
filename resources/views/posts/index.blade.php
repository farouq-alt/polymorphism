@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('partials.nav')

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Tous les Posts</h2>
                    <form method="GET" action="{{ route('posts.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Rechercher
                        </button>
                    </form>
                </div>

                @forelse($posts as $post)
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h3 class="text-xl font-semibold mb-2">
                            <a href="{{ route('posts.show', $post) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($post->content, 200) }}</p>
                        <div class="text-sm text-gray-500 dark:text-gray-500">
                            Par {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }} • {{ $post->comments->count() }} commentaire(s)
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">Aucun post trouvé.</p>
                @endforelse

                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
