#!/bin/sh
set -e

# Generate JWT keys if they don't exist
if [ ! -f /app/config/jwt/private.pem ] || [ ! -f /app/config/jwt/public.pem ]; then
    echo "Generating JWT keys..."
    mkdir -p /app/config/jwt
    openssl genpkey -out /app/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${JWT_PASSPHRASE:-change_me}
    openssl pkey -in /app/config/jwt/private.pem -out /app/config/jwt/public.pem -pubout -passin pass:${JWT_PASSPHRASE:-change_me}
    chmod 600 /app/config/jwt/private.pem
    chmod 644 /app/config/jwt/public.pem
fi

# Wait for database to be ready
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "Waiting for database..."
    sleep 2
done

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
php bin/console cache:clear --env=prod --no-debug

# Set permissions
chown -R www-data:www-data /app/var /app/public /app/config/jwt

exec "$@"
