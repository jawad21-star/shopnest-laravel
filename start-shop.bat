@echo off
REM Starts the ShopNest store at http://localhost:8002 using WAMP's PHP 8.3.
cd /d "%~dp0"
echo.
echo   Store:  http://localhost:8002
echo   Admin:  http://localhost:8002/admin
echo.
"C:\wamp64\bin\php\php8.3.28\php.exe" -d xdebug.mode=off artisan serve --port=8002
pause
