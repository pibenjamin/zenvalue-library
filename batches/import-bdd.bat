@echo off
setlocal

REM Définition des variables
set SSH_USER=benjaminpi
set SSH_HOST=ssh-benjaminpi.alwaysdata.net
set SSH_PATH=www/zenlib
set REMOTE_FILE=%SSH_PATH%/database/schema/mysql-schema.sql
set LOCAL_PATH=C:\xampp\htdocs\zbv\database\schema\mysql-schema.sql

REM Créer les dossiers nécessaires s'ils n'existent pas
mkdir "C:\xampp\htdocs\zbv\database\schema" 2>nul

REM Télécharger le fichier via SCP
echo Téléchargement du fichier MySQL schema...
scp %SSH_USER%@%SSH_HOST%:%REMOTE_FILE% "%LOCAL_PATH%"

echo Déploiement terminé et fichier téléchargé dans %LOCAL_PATH%.
pause
