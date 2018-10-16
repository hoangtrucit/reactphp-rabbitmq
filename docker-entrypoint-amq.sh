#!/bin/bash

echo "Composer install"
composer install --prefer-dist
ls -al
php src/index.php