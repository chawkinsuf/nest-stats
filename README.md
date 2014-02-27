nest-stats
==========

An application to record and display information from the nest thermostat


Installation
============

After clone execute:

```mysql
CREATE DATABASE IF NOT EXISTS `neststats`;
GRANT ALL PRIVILEGES ON `neststats`.* TO 'neststats'@'localhost' IDENTIFIED BY 'neststats' WITH GRANT OPTION;
```

```bash
php bin/composer.phar update
php artisan migrate:install
php artisan migrate
php artisan db:seed
chgrp www-data -R storage/
chmod g+w -R storage/
```
