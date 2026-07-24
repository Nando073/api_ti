#!/bin/bash

echo "=== Generando clave de aplicación ==="
php artisan key:generate --force

echo "=== Limpiando cachés antiguas ==="
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

echo "=== Generando documentación Swagger ==="
php artisan scramble:export --path=public/docs/api-docs.json || true

echo "=== Optimizando configuración (cache) ==="
php artisan config:cache

echo "=== Iniciando servidor en el puerto asignado por Railway ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}