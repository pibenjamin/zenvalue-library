@echo off
setlocal

REM Définition des variables
set SSH_USER=benjaminpi
set SSH_HOST=ssh-benjaminpi.alwaysdata.net
set SSH_PATH=www/zenlib

REM Exécuter les commandes SSH
echo Connexion SSH et exécution des commandes...
ssh %SSH_USER%@%SSH_HOST% ^
    "cd %SSH_PATH% && git pull origin main && php artisan filament:optimize-clear && php artisan filament:optimize"

echo Deployment to prod done.
pause
