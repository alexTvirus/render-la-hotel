#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Running migrations..."

#php artisan sitemap:generate

#php artisan db:seed --class=MakeSlugSeeder

#php artisan migrate:refresh --path=/database/migrations/2024_04_27_190840_add_col_slug_comics_table.php

#php artisan migrate:refresh --path=/database/migrations/2024_04_27_190852_add_col_slug_chapters_table.php
#php artisan migrate --force

#echo "Running seeders..."
#php artisan db:seed

#echo "Running vite..."
#npm install
#npm run build
