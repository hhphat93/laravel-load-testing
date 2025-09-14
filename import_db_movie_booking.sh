#!/bin/bash

# -----------------------
# Config
# -----------------------
DB_CONTAINER=mysql_master
PHP_CONTAINER=u1
DB_USER=root
DB_PASS=111
DB_NAME=movie_booking
SEEDER=MovieBookingSeeder

# -----------------------
# 1. Tạo database nếu chưa tồn tại
# -----------------------
echo "Creating database $DB_NAME if not exists..."
docker exec -i $DB_CONTAINER \
    mysql -u$DB_USER -p$DB_PASS \
    -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# -----------------------
# 2. Chạy migrate Laravel
# -----------------------
echo "Running migrations..."
docker exec -i $PHP_CONTAINER \
    php artisan migrate --path=/database/migrations/movie_booking --force

# -----------------------
# 3. Chạy seeder chỉ định
# -----------------------
echo "Seeding $SEEDER..."
docker exec -i $PHP_CONTAINER \
    php artisan db:seed --class=$SEEDER --force

echo "Done ✅"
