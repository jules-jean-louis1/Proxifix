#!/bin/bash
set -e

# Créer le répertoire des clés JWT et générer les clés si elles n'existent pas
echo "Setting up JWT keys..."
mkdir -p config/jwt
if [ ! -f "config/jwt/private.pem" ] || [ ! -f "config/jwt/public.pem" ]; then
    echo "Generating JWT keys..."
    openssl genpkey -out config/jwt/private.pem -algorithm rsa -aes256 -pass pass:${JWT_PASSPHRASE}
    openssl pkey -in config/jwt/private.pem -passin pass:${JWT_PASSPHRASE} -out config/jwt/public.pem -pubout
fi

# S'assurer que les permissions sont correctes
chown www-data:www-data config/jwt/private.pem config/jwt/public.pem
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem
echo "JWT keys setup completed!"

# Attendre que la base de données soit prête
echo "Waiting for database to be ready..."
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "Database is not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Installer les dépendances Composer si nécessaire
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Exécuter les migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures (seulement en mode dev)
if [ "$APP_ENV" = "dev" ]; then
    echo "Loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
fi

# Créer et configurer la base de test si en mode dev
if [ "$APP_ENV" = "dev" ] && [ -n "$DATABASE_TEST_URL" ]; then
    echo "Setting up test database..."
    php bin/console --env=test doctrine:database:create --if-not-exists
    php bin/console --env=test doctrine:migrations:migrate --no-interaction
fi

echo "Setup complete!"

# Exécuter la commande passée en argument (par défaut php-fpm)
exec "$@"
