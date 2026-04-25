# Schéma des Relations - Application Laravel

## Vue d'ensemble de la base de données

```
┌─────────────────────────────────────────────────────────────────┐
│                    RELATIONS POLYMORPHIQUES                      │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐
│    USERS     │
├──────────────┤
│ id           │
│ name         │
│ email        │
│ password     │
│ created_at   │
│ updated_at   │
└──────┬───────┘
       │
       │ hasMany
       ├─────────────────────────────────────┐
       │                                     │
       │                                     │
       ▼                                     ▼
┌──────────────┐                      ┌──────────────┐
│    POSTS     │                      │   VIDEOS     │
├──────────────┤                      ├──────────────┤
│ id           │                      │ id           │
│ user_id      │◄─────────────────────│ user_id      │
│ title        │                      │ title        │
│ content      │                      │ image        │
│ image        │                      │ description  │
│ is_published │                      │ url          │
│ created_at   │                      │ duration     │
│ updated_at   │                      │ is_published │
└──────┬───────┘                      │ created_at   │
       │                              │ updated_at   │
       │ morphMany                    └──────┬───────┘
       │                                     │
       │                                     │ morphMany
       │                                     │
       └─────────────┬───────────────────────┘
                     │
                     │ commentable (polymorphic)
                     │
                     ▼
              ┌──────────────┐
              │  COMMENTS    │
              ├──────────────┤
              │ id           │
              │ user_id      │◄──────┐
              │ commentable_id       │
              │ commentable_type     │
              │ content      │       │ belongsTo
              │ created_at   │       │
              │ updated_at   │       │
              └──────────────┘       │
                                     │
                              ┌──────┴───────┐
                              │    USERS     │
                              └──────────────┘
```

## Détail des relations

### 1. User → Posts (One to Many)

```php
// User.php
public function posts()
{
    return $this->hasMany(Post::class);
}

// Post.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

**Utilisation :**
```php
$user->posts;           // Tous les posts de l'utilisateur
$post->user;            // L'auteur du post
```

### 2. User → Videos (One to Many)

```php
// User.php
public function videos()
{
    return $this->hasMany(Video::class);
}

// Video.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

**Utilisation :**
```php
$user->videos;          // Toutes les vidéos de l'utilisateur
$video->user;           // L'auteur de la vidéo
```

### 3. User → Comments (One to Many)

```php
// User.php
public function comments()
{
    return $this->hasMany(Comment::class);
}

// Comment.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

**Utilisation :**
```php
$user->comments;        // Tous les commentaires de l'utilisateur
$comment->user;         // L'auteur du commentaire
```

### 4. Post → Comments (One to Many Polymorphic)

```php
// Post.php
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}

// Comment.php
public function commentable(): MorphTo
{
    return $this->morphTo();
}
```

**Utilisation :**
```php
$post->comments;        // Tous les commentaires du post
$comment->commentable;  // Le post commenté
```

**Structure en base :**
```
comments table:
┌────┬─────────┬────────────────┬──────────────────────┬─────────┐
│ id │ user_id │ commentable_id │ commentable_type     │ content │
├────┼─────────┼────────────────┼──────────────────────┼─────────┤
│ 1  │ 1       │ 1              │ App\Models\Post      │ ...     │
│ 2  │ 1       │ 1              │ App\Models\Post      │ ...     │
│ 3  │ 1       │ 1              │ App\Models\Video     │ ...     │
└────┴─────────┴────────────────┴──────────────────────┴─────────┘
```

### 5. Video → Comments (One to Many Polymorphic)

```php
// Video.php
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}

// Comment.php
public function commentable(): MorphTo
{
    return $this->morphTo();
}
```

**Utilisation :**
```php
$video->comments;       // Tous les commentaires de la vidéo
$comment->commentable;  // La vidéo commentée
```

## Flux de données

### Créer un post avec commentaires

```
User (id: 1)
    │
    ├─ create Post
    │     │
    │     └─ Post (id: 1, user_id: 1)
    │           │
    │           └─ create Comment
    │                 │
    │                 └─ Comment (
    │                       id: 1,
    │                       user_id: 1,
    │                       commentable_id: 1,
    │                       commentable_type: 'App\Models\Post'
    │                     )
```

### Créer une vidéo avec commentaires

```
User (id: 1)
    │
    ├─ create Video
    │     │
    │     └─ Video (id: 1, user_id: 1)
    │           │
    │           └─ create Comment
    │                 │
    │                 └─ Comment (
    │                       id: 2,
    │                       user_id: 1,
    │                       commentable_id: 1,
    │                       commentable_type: 'App\Models\Video'
    │                     )
```

## Requêtes SQL générées

### Récupérer un post avec ses commentaires

```php
$post = Post::with('comments')->find(1);
```

**SQL généré :**
```sql
-- Requête 1: Récupérer le post
SELECT * FROM posts WHERE id = 1;

-- Requête 2: Récupérer les commentaires
SELECT * FROM comments 
WHERE commentable_type = 'App\Models\Post' 
AND commentable_id = 1;
```

### Récupérer tous les commentaires d'un utilisateur avec leurs parents

```php
$comments = Comment::with('commentable')->where('user_id', 1)->get();
```

**SQL généré :**
```sql
-- Requête 1: Récupérer les commentaires
SELECT * FROM comments WHERE user_id = 1;

-- Requête 2: Récupérer les posts commentés
SELECT * FROM posts 
WHERE id IN (1, 2, 3);

-- Requête 3: Récupérer les vidéos commentées
SELECT * FROM videos 
WHERE id IN (1, 2);
```

## Avantages de la relation polymorphique

✅ **Réutilisabilité** : Un seul modèle Comment pour plusieurs types de contenu
✅ **Flexibilité** : Facile d'ajouter de nouveaux types commentables (Articles, Photos, etc.)
✅ **Maintenabilité** : Logique centralisée dans un seul modèle
✅ **Performance** : Eager loading optimise les requêtes
✅ **Simplicité** : Code plus propre et plus lisible

## Ajouter un nouveau type commentable

Pour ajouter un nouveau type (ex: Article) :

1. Créer le modèle et la migration
```bash
php artisan make:model Article -m
```

2. Ajouter la relation dans Article.php
```php
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

3. C'est tout ! Le modèle Comment fonctionne automatiquement avec Article

## Index recommandés pour les performances

```php
// Dans la migration comments
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->morphs('commentable'); // Crée automatiquement un index
    $table->text('content');
    $table->timestamps();
    
    // Index supplémentaire pour les requêtes fréquentes
    $table->index(['commentable_type', 'commentable_id', 'created_at']);
});
```

## Résumé des fichiers de migration

### posts table
- ✅ id, user_id, title, content, image, is_published, timestamps

### videos table
- ✅ id, user_id, title, image, description, url, duration, is_published, timestamps

### comments table
- ✅ id, user_id, commentable_id, commentable_type, content, timestamps
- ✅ Relation polymorphique via morphs('commentable')

## Commandes utiles

```bash
# Voir la structure des tables
php artisan tinker
Schema::getColumnListing('posts');
Schema::getColumnListing('videos');
Schema::getColumnListing('comments');

# Réinitialiser et relancer les migrations
php artisan migrate:fresh --seed

# Vérifier les relations
php artisan tinker
$post = Post::with('comments')->first();
$video = Video::with('comments')->first();
```
