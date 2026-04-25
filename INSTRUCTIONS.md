# Application Laravel - Posts, Vidéos et Commentaires Polymorphiques

## 1. Commandes d'installation

### Créer un projet Laravel
```bash
composer create-project laravel/laravel nom-du-projet
cd nom-du-projet
```

### Installer Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install
```

### Lancer les migrations
```bash
php artisan migrate
```

### Installer les dépendances frontend
```bash
npm install
npm run build
```

## 2. Structure de l'application

### Modèles créés
- **Post** : Articles avec titre et contenu
- **Video** : Vidéos avec titre, description, URL et durée
- **Comment** : Commentaires polymorphiques (peuvent être attachés aux posts ou vidéos)
- **User** : Utilisateurs (fourni par Breeze)

### Relations polymorphiques
Les commentaires utilisent une relation polymorphique via :
- `commentable_id` : ID de l'élément commenté
- `commentable_type` : Type de l'élément (Post ou Video)

### Policies et Gates
Chaque modèle a sa Policy pour gérer les autorisations :
- **PostPolicy** : Contrôle qui peut voir, créer, modifier, supprimer les posts
- **VideoPolicy** : Contrôle qui peut voir, créer, modifier, supprimer les vidéos
- **CommentPolicy** : Contrôle qui peut créer et supprimer les commentaires

Règles principales :
- Tout utilisateur authentifié peut créer des posts/vidéos/commentaires
- Seul l'auteur peut modifier ou supprimer son contenu
- Les posts/vidéos non publiés ne sont visibles que par leur auteur

## 3. Modifications du layout

### resources/views/layouts/app.blade.php
- Ajout de `@yield('content')` pour supporter les vues non-component
- Ajout de messages flash (success/error)
- Inclusion de la navigation

### resources/views/layouts/navigation.blade.php
- Ajout de liens vers `/posts` et `/videos` dans la navbar
- Liens disponibles dans la version desktop et mobile

### resources/views/partials/nav.blade.php
Nouveau fichier créé avec :
- Navigation secondaire pour Posts et Vidéos
- Boutons "Nouveau Post" et "Nouvelle Vidéo" (visible uniquement pour les utilisateurs connectés)
- Style cohérent avec Breeze

## 4. Fonctionnalités implémentées

### Posts
- ✅ Liste avec pagination (10 par page)
- ✅ Recherche par titre
- ✅ Création/Édition/Suppression
- ✅ Statut publié/brouillon
- ✅ Commentaires polymorphiques
- ✅ Autorisation via Policy

### Vidéos
- ✅ Liste avec pagination (10 par page)
- ✅ Recherche par titre
- ✅ Création/Édition/Suppression
- ✅ Statut publié/brouillon
- ✅ URL, description, durée
- ✅ Commentaires polymorphiques
- ✅ Autorisation via Policy

### Commentaires
- ✅ Ajout de commentaires sur posts et vidéos
- ✅ Suppression par l'auteur
- ✅ Affichage avec nom d'utilisateur et date
- ✅ Relation polymorphique

## 5. Routes disponibles

### Routes publiques
- `GET /posts` - Liste des posts
- `GET /posts/{post}` - Détail d'un post
- `GET /videos` - Liste des vidéos
- `GET /videos/{video}` - Détail d'une vidéo

### Routes authentifiées
- `GET /posts/create` - Formulaire de création de post
- `POST /posts` - Enregistrer un nouveau post
- `GET /posts/{post}/edit` - Formulaire d'édition
- `PATCH /posts/{post}` - Mettre à jour un post
- `DELETE /posts/{post}` - Supprimer un post

(Même structure pour `/videos`)

- `POST /comments` - Ajouter un commentaire
- `DELETE /comments/{comment}` - Supprimer un commentaire

## 6. Lancer l'application

```bash
# Démarrer le serveur de développement
php artisan serve

# Dans un autre terminal, compiler les assets
npm run dev
```

Accéder à l'application : http://localhost:8000

## 7. Tester l'application

1. Créer un compte via `/register`
2. Se connecter via `/login`
3. Créer un post via "Nouveau Post"
4. Créer une vidéo via "Nouvelle Vidéo"
5. Ajouter des commentaires
6. Tester les autorisations (modifier/supprimer uniquement son propre contenu)

## 8. Base de données

Tables créées :
- `users` (Breeze)
- `posts` (user_id, title, content, is_published)
- `videos` (user_id, title, description, url, duration, is_published)
- `comments` (user_id, commentable_id, commentable_type, content)

## 9. Personnalisation Breeze

- Navigation enrichie avec liens Posts et Vidéos
- Messages flash pour les actions (succès/erreur)
- Support du mode sombre (fourni par Breeze)
- Interface cohérente avec Tailwind CSS
