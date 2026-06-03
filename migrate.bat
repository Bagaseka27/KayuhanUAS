@echo off
REM Migration script for Kayuhan UAS
REM Run this file to execute the database migration

cls
echo.
echo ====================================
echo  Kayuhan UAS - Migration Script
echo ====================================
echo.

cd /d D:\laragon\www\KayuhanUAS

echo Running migration...
echo.

php migrate_manual.php

echo.
echo ====================================
echo  Migration Complete!
echo ====================================
echo.
pause
