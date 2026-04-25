@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-6">Créer un nouveau post</h2>

                <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Titre -->
                    <div class="mb-4">
                        <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                            Titre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="title" 
                            type="text" 
                            name="title" 
                            value="{{ old('title') }}" 
                            required 
                            autofocus
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            placeholder="Entrez le titre du post"
                        >
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contenu -->
                    <div class="mb-4">
                        <label for="content" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                            Contenu <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="10" 
                            required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            placeholder="Écrivez le contenu de votre post..."
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 10 caractères</p>
                    </div>

                    <!-- Image -->
                    <div class="mb-4">
                        <label for="image" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                            Image
                        </label>
                        <input 
                            id="image" 
                            type="file" 
                            name="image" 
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                                dark:file:bg-gray-700 dark:file:text-gray-300"
                        >
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF, WEBP (Max: 2 Mo)</p>
                    </div>

                    <!-- Statut de publication -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="is_published" 
                                value="1" 
                                {{ old('is_published') ? 'checked' : '' }} 
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            >
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Publier immédiatement</span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">Si non coché, le post sera enregistré comme brouillon</p>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center gap-4">
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Créer le post
                        </button>
                        <a 
                            href="{{ route('posts.index') }}" 
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-medium"
                        >
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
