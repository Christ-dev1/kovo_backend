# Kovo - Backend API

API backend de l'application **Kovo**, développée avec Laravel.
Elle fournit les services d'authentification, de gestion des utilisateurs et de profil via une API REST sécurisée avec **JWT**.

## Technologies

* **Laravel**
* **PHP 8.4**
* **JWT Authentication**
* **PostgreSQL** — base de données relationnelle
* **MySQL** — utilisé pour le développement local
* **Composer**
* **REST API**

## Base de données

Kovo utilise une **base de données relationnelle**.

### PostgreSQL

PostgreSQL est utilisé comme SGBD relationnel pour l'environnement de production.

### MySQL

MySQL peut être utilisé pour le développement local.

Configuration locale :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_kovo
DB_USERNAME=TON_USER_NAME
DB_PASSWORD=TON_PASSWORDD
```

> Les informations sensibles ne doivent pas être ajoutées au dépôt Git.

## Authentification

L'API utilise **JWT (JSON Web Token)** pour authentifier les utilisateurs.

Le fonctionnement est :

```text
Inscription
  
Création du compte
  
Génération du JWT
  
Connexion
  
Token JWT
  
Authorization: Bearer <token>
  
Accès aux routes protégées
```

## Routes API

### Inscription

```http
POST /api/register
```

Exemple :

```json
{
    "nom": "Kovo Test",
    "email": "kovo@example.com",
    "password": "password123"
}
```

### Connexion

```http
POST /api/login
```

Exemple :

```json
{
    "email": "kovo@example.com",
    "password": "password123"
}
```

Réponse :

```json
{
    "message": "Connexion réussie",
    "user": {
        "id": 1,
        "nom": "Kovo Test",
        "email": "kovo@example.com"
    },
    "token": "JWT_TOKEN",
    "token_type": "Bearer"
}
```

### Consulter le profil

```http
GET /api/profile
```

Cette route nécessite un JWT.

Header :

```http
Authorization: Bearer JWT_TOKEN
```

### Modifier le profil

```http
PUT /api/profile
```

Cette route nécessite également un JWT.

Exemple :

```json
{
    "nom": "Kovo Modifié",
    "email": "kovo.modifie@example.com"
}
```

## Validation

Les données reçues par l'API sont validées côté serveur.

Exemples :

* Le nom est obligatoire lors de l'inscription.
* L'adresse e-mail doit être valide.
* L'adresse e-mail doit être unique.
* Le mot de passe doit contenir au minimum 8 caractères.
* Les routes de profil nécessitent une authentification JWT.

Les messages de validation sont configurés en **français**.

## Installation

Cloner le projet :

```bash
git clone <URL_DU_REPOSITORY>
cd backend-kovo
```

Installer les dépendances :

```bash
composer install
```

Créer le fichier `.env` :

```bash
cp .env.example .env
```

Générer la clé Laravel :

```bash
php artisan key:generate
```

Configurer la base de données dans `.env`.

Configurer également le secret JWT :

```env
JWT_SECRET=votre_secret_jwt
JWT_ALGO=HS256
```

Lancer les migrations :

```bash
php artisan migrate
```

Pour recréer complètement la base en développement :

```bash
php artisan migrate:fresh
```

## Lancer le serveur

```bash
php artisan serve
```

L'API sera disponible à :

```text
http://127.0.0.1:8000
```

## Tests

Les endpoints peuvent être testés avec :

* Postman
* Insomnia
* cURL
* un frontend React

Exemple :

```bash
curl http://127.0.0.1:8000/api/profile \
  -H "Authorization: Bearer JWT_TOKEN"
```

## Structure principale

```text
backend-kovo/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── AuthController.php
│   └── Models/
│       └── User.php
├── config/
│   ├── auth.php
│   └── jwt.php
├── database/
│   └── migrations/
├── lang/
│   └── fr/
├── routes/
│   ├── api.php
│   └── web.php
├── .env
├── composer.json
└── artisan
```

## Sécurité

Les informations sensibles ne doivent jamais être commitées dans Git.

Ne jamais publier :

```env
APP_KEY=
DB_PASSWORD=
JWT_SECRET=
```

Utiliser plutôt un fichier `.env.example` contenant uniquement des valeurs d'exemple.

## Auteur

**KADJO BLIN ARIEL CHRIST EBENEZER**

Projet réalisé dans le cadre du développement d'une application web avec une architecture frontend/backend séparée.
