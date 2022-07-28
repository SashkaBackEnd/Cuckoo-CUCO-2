#!/bin/sh


git pull origin master

php artisan migrate

yarn dev
		
