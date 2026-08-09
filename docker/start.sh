#!/bin/bash
set -e

echo " Vérification du .env (variables Render injectées automatiquement)"

if [ -z "$APP_KEY" ]; then
    echo " ATTENTION: APP_KEY est vide, génération automatique (non recommandé en prod)"
    php artisan key:generate --force
fi

echo " Nettoyage des caches précédents"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo " Mise en cache de la configuration, des routes et des vues"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo " Lien symbolique storage (si besoin)"
php artisan storage:link || true

echo " Exécution des migrations sur Neon"
php artisan migrate --force

echo "Génération de la documentation Swagger"
php artisan l5-swagger:generate

echo "Démarrage de php-fpm"
php-fpm -D

echo " Démarrage de nginx"
nginx -g "daemon off;"
