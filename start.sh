#!/bin/bash
set -e

echo "=== Generando clave de aplicación ==="
php artisan key:generate --force

echo "=== Limpiando cachés ==="
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "=== Generando documentación Swagger con URL correcta ==="
# Forzar la URL correcta en el JSON (usa la variable APP_URL de Render)
php artisan scramble:export --path=public/docs/api-docs.json

echo "=== Iniciando servidor ==="
php artisan serve --host=0.0.0.0 --port=10000