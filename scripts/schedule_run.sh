#!/bin/bash
project_path=/var/www/cuckoo.dev-2-tech.ru/
for (( i = 0; i < 120; i++ )); do
    sleep 0.5 && php ${project_path}artisan schedule:run >> /dev/null 2>&1
done
