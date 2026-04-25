<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur de test
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Utilisateur Test',
                'password' => bcrypt('password'),
            ]
        );

        // Créer des posts
        $post1 = Post::create([
            'user_id' => $user->id,
            'title' => 'Introduction à Laravel',
            'content' => 'Laravel est un framework PHP élégant et expressif. Il facilite le développement web en fournissant une syntaxe claire et des outils puissants.',
            'is_published' => true,
        ]);

        $post2 = Post::create([
            'user_id' => $user->id,
            'title' => 'Les relations polymorphiques',
            'content' => 'Les relations polymorphiques permettent à un modèle d\'appartenir à plusieurs types de modèles sur une seule association. C\'est très utile pour des fonctionnalités comme les commentaires.',
            'is_published' => true,
        ]);

        // Créer des vidéos
        $video1 = Video::create([
            'user_id' => $user->id,
            'title' => 'Tutorial Laravel Breeze',
            'description' => 'Apprenez à installer et configurer Laravel Breeze pour l\'authentification.',
            'url' => 'https://www.youtube.com/watch?v=example1',
            'duration' => 600,
            'is_published' => true,
        ]);

        $video2 = Video::create([
            'user_id' => $user->id,
            'title' => 'Policies et Gates dans Laravel',
            'description' => 'Découvrez comment gérer les autorisations avec les Policies et Gates.',
            'url' => 'https://www.youtube.com/watch?v=example2',
            'duration' => 900,
            'is_published' => true,
        ]);

        // Créer des commentaires
        Comment::create([
            'user_id' => $user->id,
            'commentable_id' => $post1->id,
            'commentable_type' => Post::class,
            'content' => 'Excellent article ! Très instructif.',
        ]);

        Comment::create([
            'user_id' => $user->id,
            'commentable_id' => $post1->id,
            'commentable_type' => Post::class,
            'content' => 'Merci pour ce partage, j\'ai appris beaucoup de choses.',
        ]);

        Comment::create([
            'user_id' => $user->id,
            'commentable_id' => $video1->id,
            'commentable_type' => Video::class,
            'content' => 'Super tutoriel, très bien expliqué !',
        ]);

        Comment::create([
            'user_id' => $user->id,
            'commentable_id' => $video2->id,
            'commentable_type' => Video::class,
            'content' => 'Les Policies sont vraiment puissantes pour gérer les autorisations.',
        ]);
    }
}
