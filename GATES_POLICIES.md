# Gates et Policies - Guide d'utilisation

## Différence entre Gates et Policies

### Policies
Les **Policies** sont des classes qui organisent la logique d'autorisation autour d'un modèle particulier. Elles sont idéales pour gérer les actions CRUD (Create, Read, Update, Delete).

### Gates
Les **Gates** sont des closures simples qui déterminent si un utilisateur peut effectuer une action donnée. Ils sont parfaits pour des autorisations simples qui ne sont pas liées à un modèle spécifique.

## Policies implémentées

### PostPolicy
```php
// Vérifier si l'utilisateur peut voir un post
Gate::authorize('view', $post);

// Vérifier si l'utilisateur peut créer un post
Gate::authorize('create', Post::class);

// Vérifier si l'utilisateur peut modifier un post
Gate::authorize('update', $post);

// Vérifier si l'utilisateur peut supprimer un post
Gate::authorize('delete', $post);
```

### VideoPolicy
```php
// Vérifier si l'utilisateur peut voir une vidéo
Gate::authorize('view', $video);

// Vérifier si l'utilisateur peut créer une vidéo
Gate::authorize('create', Video::class);

// Vérifier si l'utilisateur peut modifier une vidéo
Gate::authorize('update', $video);

// Vérifier si l'utilisateur peut supprimer une vidéo
Gate::authorize('delete', $video);
```

### CommentPolicy
```php
// Vérifier si l'utilisateur peut créer un commentaire
Gate::authorize('create', Comment::class);

// Vérifier si l'utilisateur peut supprimer un commentaire
Gate::authorize('delete', $comment);
```

## Gates implémentés

### publish-content
Vérifie si un utilisateur peut publier du contenu (posts ou vidéos).

```php
// Dans un contrôleur
if (Gate::allows('publish-content')) {
    // L'utilisateur peut publier
}

// Dans une vue Blade
@can('publish-content')
    <button>Publier</button>
@endcan
```

### is-post-author
Vérifie si l'utilisateur est l'auteur d'un post spécifique.

```php
// Dans un contrôleur
if (Gate::allows('is-post-author', $post)) {
    // L'utilisateur est l'auteur
}

// Dans une vue Blade
@can('is-post-author', $post)
    <a href="{{ route('posts.edit', $post) }}">Modifier</a>
@endcan
```

### is-video-author
Vérifie si l'utilisateur est l'auteur d'une vidéo spécifique.

```php
// Dans un contrôleur
if (Gate::allows('is-video-author', $video)) {
    // L'utilisateur est l'auteur
}

// Dans une vue Blade
@can('is-video-author', $video)
    <a href="{{ route('videos.edit', $video) }}">Modifier</a>
@endcan
```

### moderate-content
Gate pour une future fonctionnalité de modération (actuellement désactivé).

```php
// Dans un contrôleur
if (Gate::allows('moderate-content')) {
    // L'utilisateur peut modérer le contenu
}
```

## Utilisation dans les contrôleurs

### Avec Gate::authorize()
Lance une exception si l'autorisation échoue (redirection automatique).

```php
public function update(Request $request, Post $post)
{
    Gate::authorize('update', $post);
    
    // Le code ici ne s'exécute que si l'autorisation réussit
    $post->update($request->validated());
}
```

### Avec Gate::allows() / Gate::denies()
Retourne un booléen, permet une logique conditionnelle.

```php
public function show(Post $post)
{
    if (Gate::denies('view', $post)) {
        abort(403, 'Accès non autorisé');
    }
    
    return view('posts.show', compact('post'));
}
```

## Utilisation dans les vues Blade

### Directive @can
```blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Modifier</a>
@endcan

@can('delete', $post)
    <form method="POST" action="{{ route('posts.destroy', $post) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer</button>
    </form>
@endcan
```

### Directive @cannot
```blade
@cannot('update', $post)
    <p>Vous ne pouvez pas modifier ce post.</p>
@endcannot
```

### Directive @canany
```blade
@canany(['update', 'delete'], $post)
    <div class="post-actions">
        <!-- Actions disponibles -->
    </div>
@endcanany
```

## Règles d'autorisation actuelles

### Posts
- ✅ Tout utilisateur authentifié peut créer un post
- ✅ Les posts publiés sont visibles par tous (même non connectés)
- ✅ Les posts non publiés ne sont visibles que par leur auteur
- ✅ Seul l'auteur peut modifier ou supprimer son post

### Vidéos
- ✅ Tout utilisateur authentifié peut créer une vidéo
- ✅ Les vidéos publiées sont visibles par tous (même non connectés)
- ✅ Les vidéos non publiées ne sont visibles que par leur auteur
- ✅ Seul l'auteur peut modifier ou supprimer sa vidéo

### Commentaires
- ✅ Tout utilisateur authentifié peut commenter
- ✅ Tous les commentaires sont visibles par tous
- ✅ Seul l'auteur peut supprimer son commentaire

## Ajouter un rôle admin (exemple futur)

Pour ajouter un système de rôles, vous pourriez :

1. Ajouter une colonne `is_admin` à la table users :
```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false);
});
```

2. Modifier les Gates :
```php
Gate::define('moderate-content', function (User $user) {
    return $user->is_admin;
});
```

3. Modifier les Policies pour donner plus de droits aux admins :
```php
public function delete(User $user, Post $post): bool
{
    return $user->id === $post->user_id || $user->is_admin;
}
```

## Tester les autorisations

```bash
# Dans tinker
php artisan tinker

# Créer un utilisateur
$user = User::first();

# Créer un post
$post = Post::first();

# Tester une autorisation
Gate::forUser($user)->allows('update', $post);
// true si l'utilisateur est l'auteur, false sinon

# Tester un gate simple
Gate::forUser($user)->allows('publish-content');
// true pour tout utilisateur authentifié
```
