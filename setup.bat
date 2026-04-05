@echo off
chcp 65001 >nul
cls

echo ==========================================
echo E-Antrian Lapas Sumbawa - Setup Script
echo ==========================================
echo.

if not exist "artisan" (
    echo Error: artisan file not found. Please run this script from the project root.
    exit /b 1
)

echo Step 1: Installing Composer dependencies...
call composer install --no-interaction
if errorlevel 1 (
    echo Error installing composer dependencies
    pause
    exit /b 1
)

echo.
echo Step 2: Installing NPM dependencies...
call npm install
if errorlevel 1 (
    echo Error installing npm dependencies
    pause
    exit /b 1
)

echo.
echo Step 3: Copying environment file...
if not exist ".env" (
    copy .env.example .env
    call php artisan key:generate
    echo Environment file created.
) else (
    echo .env file already exists, skipping.
)

echo.
echo Step 4: Creating storage directories...
if not exist "storage\app\public\identitas" mkdir "storage\app\public\identitas"
if not exist "storage\app\public\tickets" mkdir "storage\app\public\tickets"
if not exist "storage\app\public\images" mkdir "storage\app\public\images"

echo.
echo Step 5: Creating storage symlink...
call php artisan storage:link

echo.
echo Step 6: Running migrations...
call php artisan migrate --force
if errorlevel 1 (
    echo Error running migrations. Make sure database is configured in .env
    pause
    exit /b 1
)

echo.
echo Step 7: Running seeders...
call php artisan db:seed --force

echo.
echo Step 8: Compiling assets...
call npm run build
if errorlevel 1 (
    echo Error building assets
    pause
    exit /b 1
)

echo.
echo ==========================================
echo Setup Complete!
echo ==========================================
echo.
echo Default login credentials:
echo   Super Admin: username=admin, password=password123
echo   Operator: username=operator1, password=password123
echo.
echo Start the server with: php artisan serve
echo Access the application at: http://localhost:8000
echo.
echo Make sure your database is configured in .env file:
echo   DB_DATABASE=your_database_name
echo   DB_USERNAME=your_username
echo   DB_PASSWORD=your_password
echo.
pause
