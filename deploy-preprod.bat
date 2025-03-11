@echo off
setlocal

REM Définition des variables
set SSH_USER=benjaminpi
set SSH_HOST=ssh-benjaminpi.alwaysdata.net
set SSH_PATH=www//preprod.zenlib.agileswissknife.com

REM Exécuter les commandes SSH
echo Connexion SSH et exécution des commandes...
ssh %SSH_USER%@%SSH_HOST% ^
    "cd %SSH_PATH% && git pull origin preprod && php artisan filament:optimize-clear && php artisan filament:optimize"

echo Deployment to preprod done.
pause
