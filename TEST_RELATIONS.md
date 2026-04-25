# Test des Relations Polymorphiques

## Tester dans Tinker

Lancez tinker pour tester les relations :

```bash
php artisan tinker
```

## 1. Vérifier les données existantes

```php
// Compter les enregistrements
User::count();
Post::count();
Video::count();
Comment::count();

// Afficher tous les posts
Post::all();

// Afficher toutes les vidéos
Video::all();

// Afficher tous les commentaires
Comment::all();
```

## 2. Tester la relation User → Posts

```php
// Récupérer un utilisateur
$user = User::first();

// Afficher ses posts
$user->posts;

// Compter ses posts
$user->posts()->count();

// Créer un nouveau post
$user->posts()->create([
    'title' => 'Mon nouveau post',
    'content' => 'Contenu du post',
    'is_published' => true
]);
```

## 3. Tester la relation User → Videos

```php
// Récupérer un utilisateur
$user = User::first();

// Afficher ses vidéos
$user->videos;

// Compter ses vidéos
$user->videos()->count();

// Créer une nouvelle vidéo
$user->videos()->create([
    'title' => 'Ma nouvelle vidéo',
    'url' => 'https://youtube.com/watch?v=example',
    'description' => 'Description de la vidéo',
    'duration' => 300,
    'is_published' => true
]);
```

## 4. Tester la relation Post → Comments (Polymorphique)

```php
// Récupérer un post
$post = Post::first();

// Afficher ses commentaires
$post->comments;

// Compter ses commentaires
$post->comments()->count();

// Ajouter un commentaire au post
$post->comments()->create([
    'user_id' => 1,
    'content' => 'Super article, merci pour le partage !'
]);

// Vérifier que le commentaire est bien lié au post
$comment = $post->comments()->first();
echo $comment->commentable_type; // App\Models\Post
echo $comment->commentable_id;   // ID du post
```

## 5. Tester la relation Video → Comments (Polymorphique)

```php
// Récupérer une vidéo
$video = Video::first();

// Afficher ses commentaires
$video->comments;

// Compter ses commentaires
$video->comments()->count();

// Ajouter un commentaire à la vidéo
$video->comments()->create([
    'user_id' => 1,
    'content' => 'Excellente vidéo, très instructive !'
]);

// Vérifier que le commentaire est bien lié à la vidéo
$comment = $video->comments()->first();
echo $comment->commentable_type; // App\Models\Video
echo $comment->commentable_id;   // ID de la vidéo
```

## 6. Tester la relation Comment → Commentable (Polymorphique inverse)

```php
// Récupérer un commentaire
$comment = Comment::first();

// Afficher l'élément commenté (Post ou Video)
$parent = $comment->commentable;

// Vérifier le type
if ($parent instanceof App\Models\Post) {
    echo "Ce commentaire est sur le post : " . $parent->title;
} elseif ($parent instanceof App\Models\Video) {
    echo "Ce commentaire est sur la vidéo : " . $parent->title;
}

// Afficher l'auteur du commentaire
$comment->user->name;

// Afficher l'auteur de l'élément commenté
$comment->commentable->user->name;
```

## 7. Tester les requêtes avec Eager Loading

```php
// Charger un post avec ses commentaires et leurs auteurs
$post = Post::with('comments.user')->first();

foreach ($post->comments as $comment) {
    echo $comment->user->name . " : " . $comment->content;
}

// Charger une vidéo avec ses commentaires et leurs auteurs
$video = Video::with('comments.user')->first();

foreach ($video->comments as $comment) {
    echo $comment->user->name . " : " . $comment->content;
}

// Charger tous les posts avec le nombre de commentaires
$posts = Post::withCount('comments')->get();

foreach ($posts as $post) {
    echo $post->title . " - " . $post->comments_count . " commentaires";
}
```

## 8. Tester les filtres

```php
// Posts qui ont des commentaires
$postsWithComments = Post::has('comments')->get();

// Posts qui ont au moins 2 commentaires
$popularPosts = Post::has('comments', '>=', 2)->get();

// Posts sans commentaires
$postsWithoutComments = Post::doesntHave('comments')->get();

// Vidéos avec commentaires
$videosWithComments = Video::has('comments')->get();
```

## 9. Tester les commentaires d'un utilisateur

```php
// Récupérer tous les commentaires d'un utilisateur
$user = User::first();
$comments = $user->comments;

// Charger les commentaires avec leurs parents
$comments = $user->comments()->with('commentable')->get();

foreach ($comments as $comment) {
    $type = $comment->commentable_type === 'App\Models\Post' ? 'post' : 'vidéo';
    echo "Commentaire sur un {$type} : " . $comment->commentable->title;
}
```

## 10. Tester la suppression en cascade

```php
// Créer un post avec des commentaires
$post = Post::create([
    'user_id' => 1,
    'title' => 'Post à supprimer',
    'content' => 'Contenu du post',
    'is_published' => true
]);

$post->comments()->create([
    'user_id' => 1,
    'content' => 'Commentaire 1'
]);

$post->comments()->create([
    'user_id' => 1,
    'content' => 'Commentaire 2'
]);

// Vérifier le nombre de commentaires
$post->comments()->count(); // 2

// Supprimer le post
$post->delete();

// Les commentaires sont automatiquement supprimés (pas de cascade sur morphs)
// Note: Pour supprimer les commentaires, il faut le faire manuellement ou avec un event
```

## 11. Tester les scopes personnalisés (à ajouter dans les modèles)

```php
// Dans Post.php, ajouter :
public function scopePublished($query)
{
    return $query->where('is_published', true);
}

public function scopeWithCommentCount($query)
{
    return $query->withCount('comments');
}

// Utilisation :
$publishedPosts = Post::published()->get();
$postsWithCount = Post::withCommentCount()->get();
```

## 12. Vérifier la structure des tables

```php
// Afficher les colonnes de la table posts
Schema::getColumnListing('posts');
// ["id", "user_id", "title", "content", "image", "is_published", "created_at", "updated_at"]

// Afficher les colonnes de la table videos
Schema::getColumnListing('videos');
// ["id", "user_id", "title", "image", "description", "url", "duration", "is_published", "created_at", "updated_at"]

// Afficher les colonnes de la table comments
Schema::getColumnListing('comments');
// ["id", "user_id", "commentable_id", "commentable_type", "content", "created_at", "updated_at"]
```

## 13. Tester les validations dans les contrôleurs

Les contrôleurs valident déjà les données. Vous pouvez tester via l'interface web ou avec des requêtes HTTP :

```bash
# Créer un post (nécessite authentification)
curl -X POST http://localhost:8000/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Post",
    "content": "Contenu du test",
    "is_published": true
  }'
```

## Résultats attendus

✅ Un utilisateur peut avoir plusieurs posts
✅ Un utilisateur peut avoir plusieurs vidéos
✅ Un utilisateur peut avoir plusieurs commentaires
✅ Un post peut avoir plusieurs commentaires
✅ Une vidéo peut avoir plusieurs commentaires
✅ Un commentaire appartient à un utilisateur
✅ Un commentaire appartient à un post OU une vidéo (polymorphique)
✅ La relation polymorphique fonctionne dans les deux sens
✅ Les eager loading optimisent les requêtes
✅ Les filtres et scopes fonctionnent correctement
