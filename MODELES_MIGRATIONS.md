# Modèles et Migrations - Documentation Complète

## 3. Modèles générés

Les modèles suivants ont été créés avec leurs migrations :

```bash
php artisan make:model Post -m
php artisan make:model Video -m
php artisan make:model Comment -m
```

## 4. Migration Posts (create_posts_table)

### Structure de la table `posts`

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('content');
    $table->string('image')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});
```

### Champs :
- `id` : Clé primaire auto-incrémentée
- `user_id` : Clé étrangère vers la table users (avec cascade on delete)
- `title` : Titre du post (string)
- `content` : Contenu du post (text)
- `image` : Chemin de l'image (nullable)
- `is_published` : Statut de publication (boolean, défaut: false)
- `created_at` / `updated_at` : Timestamps automatiques

## 5. Migration Videos (create_videos_table)

### Structure de la table `videos`

```php
Schema::create('videos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->string('image')->nullable();
    $table->text('description')->nullable();
    $table->string('url');
    $table->integer('duration')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});
```

### Champs :
- `id` : Clé primaire auto-incrémentée
- `user_id` : Clé étrangère vers la table users (avec cascade on delete)
- `title` : Titre de la vidéo (string)
- `image` : Chemin de l'image/miniature (nullable)
- `description` : Description de la vidéo (text, nullable)
- `url` : URL de la vidéo (string)
- `duration` : Durée en secondes (integer, nullable)
- `is_published` : Statut de publication (boolean, défaut: false)
- `created_at` / `updated_at` : Timestamps automatiques

## 6. Migration Comments (create_comments_table)

### Structure de la table `comments`

```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->morphs('commentable');
    $table->text('content');
    $table->timestamps();
});
```

### Champs :
- `id` : Clé primaire auto-incrémentée
- `user_id` : Clé étrangère vers la table users (avec cascade on delete)
- `commentable_id` : ID de l'élément commenté (créé par morphs)
- `commentable_type` : Type de l'élément commenté (créé par morphs)
- `content` : Contenu du commentaire (text)
- `created_at` / `updated_at` : Timestamps automatiques

### Relation polymorphique avec morphs()

La méthode `morphs('commentable')` crée automatiquement deux colonnes :
- `commentable_id` : UNSIGNED BIGINT
- `commentable_type` : VARCHAR(255)

Ces colonnes permettent à un commentaire d'appartenir à différents types de modèles (Post ou Video).

## 7. Modèle Comment.php - Relation polymorphique

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = ['user_id', 'commentable_id', 'commentable_type', 'content'];

    /**
     * Relation avec l'utilisateur qui a créé le commentaire
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique - Le commentaire peut appartenir à un Post ou une Video
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

### Explication de la relation polymorphique :

La méthode `morphTo()` permet au commentaire de récupérer son parent (Post ou Video) :

```php
$comment = Comment::find(1);
$parent = $comment->commentable; // Retourne un Post ou une Video

// Vérifier le type
if ($comment->commentable_type === 'App\Models\Post') {
    // C'est un commentaire sur un post
}
```

## 8. Modèle Post.php - Relation pour récupérer les commentaires

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'image', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a créé le post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique - Récupérer tous les commentaires du post
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
```

### Utilisation :

```php
$post = Post::find(1);

// Récupérer tous les commentaires
$comments = $post->comments;

// Compter les commentaires
$count = $post->comments()->count();

// Ajouter un commentaire
$post->comments()->create([
    'user_id' => auth()->id(),
    'content' => 'Super article !'
]);

// Charger les commentaires avec leurs auteurs
$post = Post::with('comments.user')->find(1);
```

## 9. Modèle Video.php - Même relation pour les commentaires

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Video extends Model
{
    protected $fillable = ['user_id', 'title', 'image', 'description', 'url', 'duration', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a créé la vidéo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique - Récupérer tous les commentaires de la vidéo
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
```

### Utilisation :

```php
$video = Video::find(1);

// Récupérer tous les commentaires
$comments = $video->comments;

// Compter les commentaires
$count = $video->comments()->count();

// Ajouter un commentaire
$video->comments()->create([
    'user_id' => auth()->id(),
    'content' => 'Excellente vidéo !'
]);

// Charger les commentaires avec leurs auteurs
$video = Video::with('comments.user')->find(1);
```

## Schéma des relations

```
User
 ├── hasMany → Posts
 ├── hasMany → Videos
 └── hasMany → Comments

Post
 ├── belongsTo → User
 └── morphMany → Comments (commentable)

Video
 ├── belongsTo → User
 └── morphMany → Comments (commentable)

Comment
 ├── belongsTo → User
 └── morphTo → Commentable (Post ou Video)
```

## Exemples d'utilisation avancée

### Récupérer tous les commentaires d'un utilisateur avec leurs parents

```php
$user = User::find(1);
$comments = $user->comments()->with('commentable')->get();

foreach ($comments as $comment) {
    if ($comment->commentable instanceof Post) {
        echo "Commentaire sur le post : " . $comment->commentable->title;
    } elseif ($comment->commentable instanceof Video) {
        echo "Commentaire sur la vidéo : " . $comment->commentable->title;
    }
}
```

### Récupérer les posts avec le nombre de commentaires

```php
$posts = Post::withCount('comments')->get();

foreach ($posts as $post) {
    echo $post->title . " - " . $post->comments_count . " commentaires";
}
```

### Filtrer les posts qui ont des commentaires

```php
$postsWithComments = Post::has('comments')->get();
```

### Récupérer les posts avec au moins 5 commentaires

```php
$popularPosts = Post::has('comments', '>=', 5)->get();
```

## Lancer les migrations

```bash
# Lancer toutes les migrations
php artisan migrate

# Réinitialiser et relancer les migrations
php artisan migrate:fresh

# Réinitialiser et lancer avec les seeders
php artisan migrate:fresh --seed
```

## Vérifier la structure des tables

```bash
# Avec tinker
php artisan tinker

# Afficher la structure d'une table
Schema::getColumnListing('posts');
Schema::getColumnListing('videos');
Schema::getColumnListing('comments');
```
