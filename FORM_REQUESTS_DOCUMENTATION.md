# Documentation des Form Requests

## Vue d'ensemble

Les Form Requests sont des classes de validation personnalisées qui encapsulent la logique de validation et d'autorisation. Elles permettent de garder les contrôleurs propres et de réutiliser la logique de validation.

## Avantages des Form Requests

✅ **Séparation des responsabilités** : La validation est séparée du contrôleur
✅ **Réutilisabilité** : Les règles peuvent être réutilisées
✅ **Messages personnalisés** : Messages d'erreur en français
✅ **Autorisation intégrée** : Méthode `authorize()` pour les vérifications
✅ **Code propre** : Contrôleurs plus lisibles

## 1. StorePostRequest

### Fichier : `app/Http/Requests/StorePostRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'autorisation est gérée par les Policies
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'content.required' => 'Le contenu est obligatoire.',
            'content.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'content' => 'contenu',
            'image' => 'image',
            'is_published' => 'statut de publication',
        ];
    }
}
```

### Règles de validation

| Champ | Règles | Description |
|-------|--------|-------------|
| `title` | required, string, max:255 | Titre obligatoire, maximum 255 caractères |
| `content` | required, string, min:10 | Contenu obligatoire, minimum 10 caractères |
| `image` | nullable, image, mimes, max:2048 | Image optionnelle, formats acceptés, max 2 Mo |
| `is_published` | nullable, boolean | Statut de publication optionnel |

### Utilisation dans le contrôleur

```php
public function store(StorePostRequest $request)
{
    $validated = $request->validated();
    // Les données sont déjà validées
}
```

## 2. StoreCommentRequest

### Fichier : `app/Http/Requests/StoreCommentRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:1000'],
            'commentable_type' => ['required', 'string', Rule::in(['App\Models\Post', 'App\Models\Video'])],
            'commentable_id' => ['required', 'integer', 'exists:' . $this->getTableName() . ',id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

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

    protected function getTableName(): string
    {
        $type = $this->input('commentable_type');
        
        return match($type) {
            'App\Models\Post' => 'posts',
            'App\Models\Video' => 'videos',
            default => 'posts',
        };
    }
}
```

### Règles de validation

| Champ | Règles | Description |
|-------|--------|-------------|
| `content` | required, string, min:3, max:1000 | Contenu obligatoire, entre 3 et 1000 caractères |
| `commentable_type` | required, string, in:Post,Video | Type obligatoire (Post ou Video) |
| `commentable_id` | required, integer, exists | ID obligatoire et doit exister |
| `image` | nullable, image, mimes, max:2048 | Image optionnelle pour le commentaire |

### Validation dynamique

La méthode `getTableName()` détermine dynamiquement la table à vérifier selon le type :
- Si `commentable_type` = `App\Models\Post` → vérifie dans la table `posts`
- Si `commentable_type` = `App\Models\Video` → vérifie dans la table `videos`

## 3. UpdatePostRequest

### Fichier : `app/Http/Requests/UpdatePostRequest.php`

Identique à `StorePostRequest` car les mêmes règles s'appliquent pour la création et la mise à jour.

## Utilisation dans les contrôleurs

### PostController

```php
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        // Les données sont automatiquement validées
        $validated = $request->validated();
        
        // Ajouter l'ID de l'utilisateur
        $validated['user_id'] = auth()->id();
        
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }
        
        // Créer le post
        $post = Post::create($validated);
        
        return redirect()->route('posts.show', $post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();
        
        // Gérer la nouvelle image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }
        
        $post->update($validated);
        
        return redirect()->route('posts.show', $post);
    }
}
```

### CommentController

```php
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        // Les données sont automatiquement validées
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        
        // Gérer l'image optionnelle
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('comments', 'public');
        }
        
        Comment::create($validated);
        
        return back()->with('success', 'Commentaire ajouté!');
    }
}
```

## Gestion des erreurs de validation

### Affichage dans les vues Blade

```blade
<!-- Afficher une erreur spécifique -->
@error('title')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror

<!-- Afficher toutes les erreurs -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Conserver les anciennes valeurs -->
<input type="text" name="title" value="{{ old('title') }}">
```

## Gestion des fichiers uploadés

### Configuration du stockage

```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### Créer le lien symbolique

```bash
php artisan storage:link
```

Cela crée un lien de `public/storage` vers `storage/app/public`.

### Stocker un fichier

```php
// Stocker dans storage/app/public/posts
$path = $request->file('image')->store('posts', 'public');

// Stocker avec un nom personnalisé
$path = $request->file('image')->storeAs('posts', 'custom-name.jpg', 'public');
```

### Afficher une image

```blade
@if($post->image)
    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
@endif
```

### Supprimer un fichier

```php
use Illuminate\Support\Facades\Storage;

if ($post->image) {
    Storage::disk('public')->delete($post->image);
}
```

## Tester les validations

### Avec Tinker

```bash
php artisan tinker
```

```php
// Créer une requête de test
$request = new \App\Http\Requests\StorePostRequest();
$request->merge([
    'title' => 'Test',
    'content' => 'Contenu trop court'
]);

// Valider
$validator = Validator::make($request->all(), $request->rules());
$validator->fails(); // true si échec
$validator->errors(); // Messages d'erreur
```

### Avec des tests unitaires

```php
public function test_post_creation_requires_title()
{
    $response = $this->post('/posts', [
        'content' => 'Contenu du post',
    ]);

    $response->assertSessionHasErrors('title');
}

public function test_post_creation_with_valid_data()
{
    $response = $this->post('/posts', [
        'title' => 'Mon titre',
        'content' => 'Mon contenu de plus de 10 caractères',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'title' => 'Mon titre',
    ]);
}
```

## Règles de validation courantes

| Règle | Description | Exemple |
|-------|-------------|---------|
| `required` | Champ obligatoire | `'title' => 'required'` |
| `nullable` | Champ optionnel | `'image' => 'nullable'` |
| `string` | Doit être une chaîne | `'title' => 'string'` |
| `integer` | Doit être un entier | `'age' => 'integer'` |
| `boolean` | Doit être un booléen | `'is_published' => 'boolean'` |
| `email` | Doit être un email | `'email' => 'email'` |
| `url` | Doit être une URL | `'website' => 'url'` |
| `min:n` | Longueur/valeur minimale | `'content' => 'min:10'` |
| `max:n` | Longueur/valeur maximale | `'title' => 'max:255'` |
| `image` | Doit être une image | `'photo' => 'image'` |
| `mimes:x,y` | Types MIME acceptés | `'image' => 'mimes:jpeg,png'` |
| `exists:table,column` | Doit exister en BDD | `'user_id' => 'exists:users,id'` |
| `unique:table,column` | Doit être unique | `'email' => 'unique:users'` |
| `in:x,y,z` | Doit être dans la liste | `'role' => 'in:admin,user'` |

## Résumé

✅ **StorePostRequest** : Validation pour créer un post (title, content, image)
✅ **UpdatePostRequest** : Validation pour mettre à jour un post
✅ **StoreCommentRequest** : Validation pour créer un commentaire (content, type, id, image)
✅ Messages d'erreur personnalisés en français
✅ Gestion des fichiers uploadés (images)
✅ Validation dynamique selon le type (Post ou Video)
✅ Contrôleurs propres et maintenables
