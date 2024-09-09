#!/usr/bin/env bash

echo "====================== Pull From Master ======================"
git pull origin master

echo "====================== Composer Install ======================"
composer install --no-dev

echo "====================== Migration ======================"
php artisan migrate
