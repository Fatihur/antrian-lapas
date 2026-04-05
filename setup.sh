#!/bin/bash

echo "=========================================="
echo "E-Antrian Lapas Sumbawa - Setup Script"
echo "=========================================="
echo ""

# Check if running in correct directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from the project root."
    exit 1
fi

echo "Step 1: Installing Composer dependencies..."
composer install --no-interaction

echo ""
echo "Step 2: Installing NPM dependencies..."
npm install

echo ""
echo "Step 3: Copying environment file..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
    echo "Environment file created."
else
    echo ".env file already exists, skipping."
fi

echo ""
echo "Step 4: Creating storage directories..."
mkdir -p storage/app/public/identitas
mkdir -p storage/app/public/tickets
mkdir -p storage/app/public/images

echo ""
echo "Step 5: Creating storage symlink..."
php artisan storage:link

echo ""
echo "Step 6: Running migrations..."
php artisan migrate --force

echo ""
echo "Step 7: Running seeders..."
php artisan db:seed --force

echo ""
echo "Step 8: Compiling assets..."
npm run build

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Default login credentials:"
echo "  Super Admin: username=admin, password=password123"
echo "  Operator: username=operator1, password=password123"
echo ""
echo "Start the server with: php artisan serve"
echo "Access the application at: http://localhost:8000"
echo ""
echo "Make sure your database is configured in .env file:"
echo "  DB_DATABASE=your_database_name"
echo "  DB_USERNAME=your_username"
echo "  DB_PASSWORD=your_password"
