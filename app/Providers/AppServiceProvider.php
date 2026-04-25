<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate pour vérifier si un utilisateur peut publier du contenu
        Gate::define('publish-content', function (User $user) {
            // Tous les utilisateurs authentifiés peuvent publier
            return true;
        });

        // Gate pour vérifier si un utilisateur est l'auteur d'un post
        Gate::define('is-post-author', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });

        // Gate pour vérifier si un utilisateur est l'auteur d'une vidéo
        Gate::define('is-video-author', function (User $user, Video $video) {
            return $user->id === $video->user_id;
        });

        // Gate pour modérer le contenu (exemple pour un futur rôle admin)
        Gate::define('moderate-content', function (User $user) {
            // Pour l'instant, personne ne peut modérer
            // Plus tard, on pourrait vérifier : return $user->is_admin;
            return false;
        });
    }
}

