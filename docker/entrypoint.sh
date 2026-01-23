#!/bin/sh
set -e

# Render sets PORT automatically, use it for nginx
PORT=${PORT:-8080}
sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf

# Create minimal .env file if it doesn't exist (required by Symfony)
if [ ! -f /app/.env ]; then
    echo "Creating .env file..."
    APP_SECRET_VALUE=${APP_SECRET:-$(openssl rand -hex 32)}
    cat > /app/.env <<EOF
APP_ENV=prod
APP_SECRET=$APP_SECRET_VALUE
DATABASE_URL=${DATABASE_URL}
JWT_PASSPHRASE=${JWT_PASSPHRASE}
EOF
    echo ".env file created with DATABASE_URL=${DATABASE_URL:0:20}..."
fi

# Generate JWT keys if they don't exist
if [ ! -f /app/config/jwt/private.pem ] || [ ! -f /app/config/jwt/public.pem ]; then
    echo "Generating JWT keys..."
    mkdir -p /app/config/jwt
    openssl genpkey -out /app/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${JWT_PASSPHRASE:-change_me}
    openssl pkey -in /app/config/jwt/private.pem -out /app/config/jwt/public.pem -pubout -passin pass:${JWT_PASSPHRASE:-change_me}
    chmod 600 /app/config/jwt/private.pem
    chmod 644 /app/config/jwt/public.pem
fi

# Clear cache first (doesn't need database)
php bin/console cache:clear --env=prod --no-debug || echo "Cache clear failed, continuing..."

# Wait for database to be ready (with shorter timeout) - don't block startup
if [ -n "$DATABASE_URL" ]; then
    timeout=30
    elapsed=0
    echo "Checking database connection..."
    until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1 || [ $elapsed -ge $timeout ]; do
        echo "Waiting for database... ($elapsed/$timeout seconds)"
        sleep 2
        elapsed=$((elapsed + 2))
    done

    if php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; then
        echo "Database connection successful! Running migrations..."
        php bin/console doctrine:migrations:migrate --no-interaction || echo "Migrations failed, continuing..."
    else
        echo "Warning: Database not ready yet (timeout after $timeout seconds)."
        echo "Application will start anyway. Migrations will be skipped for now."
        echo "You can run migrations manually later when database is ready."
    fi
else
    echo "WARNING: DATABASE_URL is not set, skipping database operations"
fi

# Set permissions
chown -R www-data:www-data /app/var /app/public /app/config/jwt

exec "$@"
