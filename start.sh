#!/bin/bash

echo "=== Generando clave de aplicación ==="
php artisan key:generate --force

echo "=== Limpiando cachés ==="
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

echo "=== Generando documentación Swagger ==="
# Si falla, no detiene el inicio del servidor
php artisan scramble:export --path=public/docs/api-docs.json || true

echo "=== Iniciando servidor ==="
php artisan serve --host=0.0.0.0 --port=10000