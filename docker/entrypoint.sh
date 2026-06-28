#!/bin/sh
set -e

php artisan package:discover --ansi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

echo "Waiting for database..."
until php -r "
try {
    new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    sleep 1
done

echo "Database ready."

php artisan migrate --force
php artisan articles:fetch

php artisan serve --host=0.0.0.0 --port=8000
