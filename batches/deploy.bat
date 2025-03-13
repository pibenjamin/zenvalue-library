@echo off
setlocal

REM Définition des variables
set SSH_USER=benjaminpi
set SSH_HOST=ssh-benjaminpi.alwaysdata.net
set SSH_PATH=www/zenlib

REM Exécuter les commandes SSH
echo Connexion SSH et execution des commandes...
ssh %SSH_USER%@%SSH_HOST% ^
    "cd %SSH_PATH% && git pull origin main && php artisan filament:optimize-clear && php artisan filament:optimize && php artisan migrate"

echo Deployment to prod done.
pause
