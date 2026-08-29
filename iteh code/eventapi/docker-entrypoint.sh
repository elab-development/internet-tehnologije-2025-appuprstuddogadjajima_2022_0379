#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  if [ -f .env.docker ]; then
    cp .env.docker .env
  else
    cp .env.example .env
  fi
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache || true

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Waiting for MySQL..."
php -r '
$host = getenv("DB_HOST") ?: "db";
$port = getenv("DB_PORT") ?: "3306";
$user = getenv("DB_USERNAME") ?: "eventhub";
$pass = getenv("DB_PASSWORD") ?: "";
for ($i = 0; $i < 30; $i++) {
    try {
        new PDO(sprintf("mysql:host=%s;port=%s", $host, $port), $user, $pass);
        exit(0);
    } catch (Throwable $e) {
        fwrite(STDERR, "database not ready, retrying..." . PHP_EOL);
        sleep(2);
    }
}
fwrite(STDERR, "database did not become ready" . PHP_EOL);
exit(1);
'

php artisan config:clear
php artisan migrate --force
php artisan l5-swagger:generate || true

exec php artisan serve --host=0.0.0.0 --port=8000
