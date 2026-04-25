@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('partials.nav')

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Toutes les Vidéos</h2>
                    <form method="GET" action="{{ route('videos.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                            Rechercher
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($videos as $video)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                                </svg>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2">
                                    <a href="{{ route('videos.show', $video) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $video->title }}
                                    </a>
                                </h3>
                                @if($video->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ Str::limit($video->description, 100) }}</p>
                                @endif
                                <div class="text-xs text-gray-500 dark:text-gray-500">
                                    Par {{ $video->user->name }} • {{ $video->created_at->diffForHumans() }}
                                    @if($video->duration)
                                        • {{ gmdate('i:s', $video->duration) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 col-span-full">Aucune vidéo trouvée.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
