#!/bin/bash

echo "Composer install"
composer install --prefer-dist
ls -al src
php src/Consumer.php