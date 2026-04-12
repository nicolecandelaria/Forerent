#!/bin/bash
set -e

# 1. Ensure permissions are correct
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2. Wait for Database (Crucial for Cloud Deploys)
# This prevents the "Connection Refused" crash on Render
echo "Waiting for database connection..."
until php artisan db:monitor --databases=mysql > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 2
done
echo "Database is up!"

# 3. Production Optimizations
if [ "${APP_ENV}" = "production" ]; then
    echo "Running in production mode..."
    php artisan migrate --force
    # 'optimize' handles config, routes, and views in one go
    php artisan optimize
    php artisan storage:link
else
    echo "Running in development mode..."
    php artisan migrate
fi

<<<<<<< HEAD
# 4. Start the Engine
echo "Starting Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
=======
chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache

# On production, default to stderr logging unless explicitly configured.
if [ "${APP_ENV}" = "production" ] && [ -z "${LOG_CHANNEL}" ]; then
	export LOG_CHANNEL=stderr
fi

if [ "${APP_ENV}" = "production" ] && [ "${LOG_CHANNEL}" = "stack" ] && [ -z "${LOG_STACK}" ]; then
	export LOG_STACK=stderr
fi

# Build Firebase credentials file from env when secret file mounts are not readable.
if [ -n "${FIREBASE_CREDENTIALS_BASE64}" ] || [ -n "${FIREBASE_CREDENTIALS_JSON}" ]; then
	FIREBASE_CREDENTIALS_FILE="/var/www/storage/app/firebase-adminsdk.json"
	mkdir -p "$(dirname "$FIREBASE_CREDENTIALS_FILE")"

	if [ -n "${FIREBASE_CREDENTIALS_BASE64}" ]; then
		printf '%s' "${FIREBASE_CREDENTIALS_BASE64}" | base64 --decode > "$FIREBASE_CREDENTIALS_FILE"
	else
		printf '%s' "${FIREBASE_CREDENTIALS_JSON}" > "$FIREBASE_CREDENTIALS_FILE"
	fi

	chmod 600 "$FIREBASE_CREDENTIALS_FILE"

	if id -u www-data >/dev/null 2>&1; then
		chown www-data:www-data "$FIREBASE_CREDENTIALS_FILE"
	fi

	export FIREBASE_CREDENTIALS="$FIREBASE_CREDENTIALS_FILE"
fi

if [ -n "${FIREBASE_CREDENTIALS}" ] && [ ! -r "${FIREBASE_CREDENTIALS}" ]; then
	echo "[WARN] FIREBASE_CREDENTIALS is set but not readable by runtime user: ${FIREBASE_CREDENTIALS}" >&2
fi

# php artisan migrate --force
# Run database migration and seeding asynchronously
# Run migrations first (must complete before app starts)
php artisan migrate:fresh --force

# Cache/clear tasks (fast, run synchronously)
php artisan config:cache
php artisan storage:link
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Seed in background — logs will still stream to stdout/render
php artisan db:seed --force &

# Start supervisor (app is available with empty DB during seed)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
>>>>>>> 07d21e8eb6667c9448ea154eec15c45164d8f1e1
