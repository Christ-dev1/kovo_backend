
# Kovo - Backend API

API backend de l'application Kovo, développée avec Laravel.
Elle fournit les services d'authentification, de gestion des utilisateurs et de profil via une API REST sécurisée avec JWT.

## Technologies

* Laravel
* PHP 8.4
* JWT Authentication (php-open-source-saver/jwt-auth)
* PostgreSQL (Neon) — base de données en production
* MySQL — utilisé pour le développement local
* Docker — conteneurisation (nginx + php-fpm)
* Render — hébergement
* Swagger / OpenAPI (L5-Swagger) — documentation interactive de l'API
* Composer
* REST API

## Base de données

Kovo utilise une base de données relationnelle.

### PostgreSQL (production)

PostgreSQL, hébergé sur Neon, est utilisé comme SGBD relationnel pour l'environnement de production.

Configuration production (variables d'environnement sur Render) :

```env
DB_CONNECTION=pgsql
DB_HOST=<host_neon>
DB_PORT=5432
DB_DATABASE=<nom_base>
DB_USERNAME=<utilisateur>
DB_PASSWORD=<mot_de_passe>
DB_SSLMODE=require
```

### MySQL (développement local)

MySQL peut être utilisé pour le développement local.

Configuration locale :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_kovo
DB_USERNAME=christ
DB_PASSWORD=********
```

Les informations sensibles ne doivent pas être ajoutées au dépôt Git.

## Authentification

L'API utilise JWT (JSON Web Token) pour authentifier les utilisateurs.

Le fonctionnement est le suivant :

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

Exemple de requête :

```json
{
    "nom": "Kovo Test",
    "email": "kovo@example.com",
    "password": "password123"
}
```

Réponse (201) :

```json
{
    "message": "Inscription réussie",
    "user": {
        "id": 1,
        "nom": "Kovo Test",
        "email": "kovo@example.com"
    },
    "token": "JWT_TOKEN",
    "token_type": "Bearer"
}
```

### Connexion

```http
POST /api/login
```

Exemple de requête :

```json
{
    "email": "kovo@example.com",
    "password": "password123"
}
```

Réponse (200) :

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

Réponse (200) :

```json
{
    "user": {
        "id": 1,
        "nom": "Kovo Test",
        "email": "kovo@example.com"
    }
}
```

### Modifier le profil

```http
PUT /api/profile
```

Cette route nécessite également un JWT.

Exemple de requête :

```json
{
    "nom": "Kovo Modifié",
    "email": "kovo.modifie@example.com"
}
```

Réponse (200) :

```json
{
    "message": "Profil mis à jour",
    "user": {
        "id": 1,
        "nom": "Kovo Modifié",
        "email": "kovo.modifie@example.com"
    }
}
```

## Validation

Les données reçues par l'API sont validées côté serveur.

Règles principales :

* Le nom est obligatoire lors de l'inscription.
* L'adresse e-mail doit être valide.
* L'adresse e-mail doit être unique.
* Le mot de passe doit contenir au minimum 8 caractères.
* Les routes de profil nécessitent une authentification JWT.

Les messages de validation sont configurés en français.

## Documentation interactive (Swagger)

La documentation complète de l'API est générée automatiquement à partir des attributs PHP présents dans les contrôleurs (`OpenApi\Attributes`), via le package L5-Swagger.

Accès en local :

```text
http://127.0.0.1:8000/api/documentation
```

Accès en production :

```text
https://kovo-backend-0pmr.onrender.com/api/documentation
```

L'interface permet de consulter chaque route, son schéma de requête/réponse, et de tester les appels directement (bouton "Authorize" pour renseigner le token JWT sur les routes protégées).

Pour régénérer la documentation manuellement :

```bash
php artisan l5-swagger:generate
```

## Installation (développement local)

Cloner le projet :

```bash
git clone <URL_DU_REPOSITORY>
cd kovo_backend
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

## Lancer le serveur en local

```bash
php artisan serve
```

L'API sera disponible à :

```text
http://127.0.0.1:8000
```

## Déploiement (Docker + Render + Neon)

Le projet est conteneurisé avec Docker (nginx + php-fpm) et déployé sur Render, connecté à une base PostgreSQL hébergée sur Neon.

Fichiers de configuration :

```text
Dockerfile
docker/
├── nginx.conf
├── php-fpm.conf
└── start.sh
```

Le script `docker/start.sh` exécute au démarrage du conteneur :

1. Mise en cache de la configuration, des routes et des vues
2. Lien symbolique du storage
3. Exécution des migrations sur Neon
4. Génération de la documentation Swagger
5. Démarrage de php-fpm puis de nginx

Variables d'environnement à configurer sur Render :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kovo-backend-0pmr.onrender.com

DB_CONNECTION=pgsql
DB_HOST=<host_neon>
DB_PORT=5432
DB_DATABASE=<nom_base>
DB_USERNAME=<utilisateur>
DB_PASSWORD=<mot_de_passe>
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

JWT_SECRET=<secret_jwt>
JWT_ALGO=HS256

L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=https://kovo-backend-0pmr.onrender.com
```

## Tests

Les endpoints peuvent être testés avec :

* Postman
* Insomnia
* cURL
* Swagger UI (`/api/documentation`)
* un frontend React

Exemple avec cURL :

```bash
curl https://kovo-backend-0pmr.onrender.com/api/profile \
  -H "Authorization: Bearer JWT_TOKEN"
```

## Structure principale

```text
kovo_backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── AuthController.php
│   └── Models/
│       └── User.php
├── config/
│   ├── auth.php
│   ├── jwt.php
│   └── l5-swagger.php
├── database/
│   └── migrations/
├── docker/
│   ├── nginx.conf
│   ├── php-fpm.conf
│   └── start.sh
├── lang/
│   └── fr/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   └── api-docs/
├── .env
├── Dockerfile
├── composer.json
└── artisan
```

## Securite

Les informations sensibles ne doivent jamais être commitées dans Git.

Ne jamais publier :

```env
APP_KEY=
DB_PASSWORD=
JWT_SECRET=
```

Utiliser plutôt un fichier `.env.example` contenant uniquement des valeurs d'exemple.

## Auteur

KADJO BLIN ARIEL CHRIST EBENEZER

Projet réalisé dans le cadre du développement d'une application web avec une architecture frontend/backend séparée.
