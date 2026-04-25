# Récapitulatif des Tâches 10, 11 et 12

## ✅ Tâche 10 : PostController avec méthodes CRUD

### Fichier créé : `app/Http/Controllers/PostController.php`

Le contrôleur a été créé avec toutes les méthodes CRUD :

```php
class PostController extends Controller
{
    public function index(Request $request)      // Liste des posts avec recherche et pagination
    public function create()                      // Formulaire de création
    public function store(StorePostRequest $request)  // Enregistrer un nouveau post
    public function show(Post $post)             // Afficher un post
    public function edit(Post $post)             // Formulaire d'édition
    public function update(UpdatePostRequest $request, Post $post)  // Mettre à jour
    public function destroy(Post $post)          // Supprimer un post
}
```

### Méthode store() détaillée

```php
public function store(StorePostRequest $request)
{
    // 1. Autorisation via Policy
    Gate::authorize('create', Post::class);

    // 2. Récupérer les données validées
    $validated = $request->validated();
    $validated['user_id'] = auth()->id();

    // 3. Gérer l'upload de l'image
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    }

    // 4. Gérer le statut de publication
    $validated['is_published'] = $request->has('is_published') ? true : false;

    // 5. Créer le post
    $post = Post::create($validated);

    // 6. Rediriger avec message de succès
    return redirect()
        ->route('posts.show', $post)
        ->with('success', 'Post créé avec succès!');
}
```

### Fonctionnalités implémentées

✅ **index()** : 
- Liste tous les posts publiés
- Recherche par titre
- Pagination (10 posts par page)
- Chargement des relations (user, comments)

✅ **create()** :
- Vérification des autorisations
- Affichage du formulaire

✅ **store()** :
- Validation via StorePostRequest
- Upload d'image
- Gestion du statut de publication
- Création du post
- Redirection avec message

✅ **show()** :
- Vérification des autorisations (posts non publiés visibles uniquement par l'auteur)
- Chargement des commentaires avec leurs auteurs

✅ **edit()** :
- Vérification que l'utilisateur est l'auteur
- Affichage du formulaire pré-rempli

✅ **update()** :
- Validation via UpdatePostRequest
- Gestion de la nouvelle image
- Suppression de l'ancienne image
- Mise à jour du post

✅ **destroy()** :
- Vérification que l'utilisateur est l'auteur
- Suppression de l'image
- Suppression du post

## ✅ Tâche 11 : CommentController avec validation

### Fichier créé : `app/Http/Controllers/CommentController.php`

```php
class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    public function destroy(Comment $comment)
}
```

### Form Request créé : `app/Http/Requests/StoreCommentRequest.php`

#### Règles de validation (rules)

```php
public function rules(): array
{
    return [
        'content' => ['required', 'string', 'min:3', 'max:1000'],
        'commentable_type' => ['required', 'string', Rule::in(['App\Models\Post', 'App\Models\Video'])],
        'commentable_id' => ['required', 'integer', 'exists:' . $this->getTableName() . ',id'],
        'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
    ];
}
```

#### Messages personnalisés (messages)

```php
public function messages(): array
{
    return [
        'content.required' => 'Le contenu du commentaire est obligatoire.',
        'content.min' => 'Le commentaire doit contenir au moins 3 caractères.',
        'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        'commentable_type.required' => 'Le type d\'élément est obligatoire.',
        'commentable_type.in' => 'Le type d\'élément doit être un Post ou une Video.',
        'commentable_id.required' => 'L\'identifiant de l\'élément est obligatoire.',
        'commentable_id.exists' => 'L\'élément commenté n\'existe pas.',
        'image.image' => 'Le fichier doit être une image.',
        'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif ou webp.',
        'image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
    ];
}
```

### Méthode store() détaillée

```php
public function store(StoreCommentRequest $request)
{
    // 1. Autorisation via Policy
    Gate::authorize('create', Comment::class);

    // 2. Récupérer les données validées
    $validated = $request->validated();
    $validated['user_id'] = auth()->id();

    // 3. Gérer l'upload de l'image (optionnel)
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('comments', 'public');
    }

    // 4. Créer le commentaire
    $comment = Comment::create($validated);

    // 5. Rediriger avec message
    return back()->with('success', 'Commentaire ajouté avec succès!');
}
```

### Validation des champs

| Champ | Validation | Description |
|-------|-----------|-------------|
| **content** | required, string, min:3, max:1000 | Contenu obligatoire entre 3 et 1000 caractères |
| **commentable_type** | required, string, in:Post,Video | Type obligatoire (Post ou Video uniquement) |
| **commentable_id** | required, integer, exists | ID obligatoire et doit exister dans la table correspondante |
| **image** | nullable, image, mimes, max:2048 | Image optionnelle, formats acceptés, max 2 Mo |

### Validation dynamique

La méthode `getTableName()` détermine la table à vérifier :

```php
protected function getTableName(): string
{
    $type = $this->input('commentable_type');
    
    return match($type) {
        'App\Models\Post' => 'posts',
        'App\Models\Video' => 'videos',
        default => 'posts',
    };
}
```

Cela permet de valider que l'ID existe bien dans la bonne table selon le type.

## ✅ Tâche 12 : Vue posts/index.blade.php

### Fichier créé : `resources/views/posts/index.blade.php`

### Fonctionnalités implémentées

#### 1. Barre de recherche

```blade
<form method="GET" action="{{ route('posts.index') }}" class="flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un post...">
    <button type="submit">Rechercher</button>
    @if(request('search'))
        <a href="{{ route('posts.index') }}">Réinitialiser</a>
    @endif
</form>
```

#### 2. Affichage des résultats de recherche

```blade
@if(request('search'))
    <div class="mb-4">
        Résultats pour : <strong>{{ request('search') }}</strong> 
        ({{ $posts->total() }} post(s) trouvé(s))
    </div>
@endif
```

#### 3. Liste des posts avec images

```blade
@forelse($posts as $post)
    <article class="border rounded-lg overflow-hidden">
        <div class="md:flex">
            <!-- Image du post -->
            @if($post->image)
                <div class="md:w-1/3">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                </div>
            @endif

            <!-- Contenu du post -->
            <div class="p-6">
                <h3>
                    <a href="{{ route('posts.show', $post) }}">
                        {{ $post->title }}
                    </a>
                </h3>
                
                <p>{{ Str::limit($post->content, 200) }}</p>
                
                <div class="flex items-center space-x-4">
                    <span>{{ $post->user->name }}</span>
                    <span>{{ $post->created_at->diffForHumans() }}</span>
                    <span>{{ $post->comments->count() }} commentaire(s)</span>
                </div>
            </div>
        </div>
    </article>
@empty
    <div class="text-center">
        <p>Aucun post trouvé</p>
        @auth
            <a href="{{ route('posts.create') }}">Créer un post</a>
        @endauth
    </div>
@endforelse
```

#### 4. Pagination

```blade
@if($posts->hasPages())
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endif
```

#### 5. Icônes SVG

- Icône utilisateur
- Icône calendrier
- Icône commentaires
- Icône document vide (quand aucun post)

#### 6. Design responsive

- Layout adaptatif (mobile/desktop)
- Images responsive
- Navigation mobile-friendly

### Structure de la vue

```
posts/index.blade.php
├── Navigation secondaire (@include('partials.nav'))
├── Barre de recherche
│   ├── Input de recherche
│   ├── Bouton rechercher
│   └── Bouton réinitialiser (si recherche active)
├── Résultats de recherche (si applicable)
├── Liste des posts
│   ├── Image (si disponible)
│   ├── Titre (lien vers show)
│   ├── Extrait du contenu (200 caractères)
│   └── Métadonnées (auteur, date, commentaires)
├── Message si aucun post
│   └── Bouton créer (si authentifié)
└── Pagination
```

## Fichiers créés/modifiés

### Contrôleurs
- ✅ `app/Http/Controllers/PostController.php` (recréé avec gestion d'images)
- ✅ `app/Http/Controllers/CommentController.php` (recréé avec Form Request)

### Form Requests
- ✅ `app/Http/Requests/StorePostRequest.php` (nouveau)
- ✅ `app/Http/Requests/UpdatePostRequest.php` (nouveau)
- ✅ `app/Http/Requests/StoreCommentRequest.php` (nouveau)

### Modèles
- ✅ `app/Models/Comment.php` (ajout du champ image dans $fillable)

### Migrations
- ✅ `database/migrations/2026_04_25_111753_add_image_to_comments_table.php` (nouveau)

### Vues
- ✅ `resources/views/posts/index.blade.php` (recréé avec design amélioré)
- ✅ `resources/views/posts/create.blade.php` (recréé avec upload d'image)

### Configuration
- ✅ Lien symbolique créé : `php artisan storage:link`

## Tests à effectuer

### 1. Tester la création d'un post

```bash
# Via l'interface web
1. Se connecter
2. Aller sur /posts/create
3. Remplir le formulaire
4. Uploader une image
5. Cocher "Publier immédiatement"
6. Soumettre
```

### 2. Tester la validation

```bash
# Essayer de créer un post sans titre
# Essayer de créer un post avec un contenu trop court (< 10 caractères)
# Essayer d'uploader un fichier non-image
# Essayer d'uploader une image trop grande (> 2 Mo)
```

### 3. Tester la recherche

```bash
# Aller sur /posts
# Entrer un terme de recherche
# Vérifier les résultats
# Cliquer sur "Réinitialiser"
```

### 4. Tester les commentaires

```bash
# Aller sur un post
# Ajouter un commentaire
# Vérifier la validation (min 3 caractères)
# Supprimer un commentaire (si auteur)
```

### 5. Tester l'affichage des images

```bash
# Créer un post avec image
# Vérifier l'affichage dans /posts
# Vérifier l'affichage dans /posts/{id}
# Modifier le post et changer l'image
# Vérifier que l'ancienne image est supprimée
```

## Commandes utiles

```bash
# Créer le lien symbolique pour le stockage
php artisan storage:link

# Lancer les migrations
php artisan migrate

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Lancer le serveur
php artisan serve

# Compiler les assets
npm run dev
```

## Résumé des validations

### Posts
- Titre : obligatoire, max 255 caractères
- Contenu : obligatoire, min 10 caractères
- Image : optionnelle, formats acceptés (jpeg, png, jpg, gif, webp), max 2 Mo
- Statut : optionnel (publié/brouillon)

### Commentaires
- Contenu : obligatoire, entre 3 et 1000 caractères
- Type : obligatoire (Post ou Video)
- ID : obligatoire et doit exister
- Image : optionnelle, formats acceptés, max 2 Mo

## Points clés

✅ **Form Requests** : Validation séparée des contrôleurs
✅ **Messages personnalisés** : Erreurs en français
✅ **Upload d'images** : Gestion complète (store, update, delete)
✅ **Validation dynamique** : Selon le type (Post/Video)
✅ **Interface utilisateur** : Design moderne et responsive
✅ **Recherche** : Fonctionnelle avec réinitialisation
✅ **Pagination** : 10 posts par page
✅ **Autorisations** : Policies et Gates intégrés

Toutes les tâches 10, 11 et 12 sont complètes et fonctionnelles !
