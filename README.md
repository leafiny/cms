# Leafiny

Leafiny is a CMS who not bloated with features that you don't need.

* Less is better. Leafiny can generate a page in less than 10ms.
* Add modules and use technology as you want. Override core modules if needed.
* Leafiny works in standalone. You don't need composer, npm, gulp or any other third party software, but you are free to use it if needed.
* Secure with isolated web root and safe Content Security Policy (avoid inline JS).

# Requirements

* Apache 2.4+
* PHP 7.2+
* MySQL 5.6+

## PHP extensions

* gd
* pdo_mysql
* json
* mbstring
* intl

# Documentation

The documentation is available on [Leafiny documentation](https://docs.leafiny.com/) site.

# Demo

* [Demo Admin EN](https://demo.en.leafiny.com/admin/leafiny/)
* [Demo Admin FR](https://demo.fr.leafiny.com/admin/leafiny/)
* [Demo Frontend EN](https://demo.en.leafiny.com)

# Installation

## Environment variables

In **vhost** config file, set variables for the current environment.

**DEVELOPER_MODE**

```apacheconf
SetEnv DEVELOPER_MODE [0|1]
```

In developer mode all errors are displayed.

**ENVIRONMENT**

```apacheconf
SetEnv ENVIRONMENT [dev|preprod|default]
```

Environment variable is used to load local config file in **etc** directory (config.**X**.php).

**LANGUAGE**

```apacheconf
SetEnv LANGUAGE [en_US|fr_FR|de_DE|...]
```

This variable is for multi-language websites. You create a host per language (en.website.com, fr.webiste.com...).

**EXAMPLE**

Basic non secure Apache virtual host for a Leafiny website :

```apacheconfig
<VirtualHost *:80>
    ServerAdmin admin@localhost

    DocumentRoot /var/www/example/pub
    ServerName www.example.com

    SetEnv DEVELOPER_MODE 0
    SetEnv ENVIRONMENT default
    SetEnv LANGUAGE en_US

    <Directory /var/www/example/pub>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.example.log
    CustomLog ${APACHE_LOG_DIR}/access.example.log combined
</VirtualHost>
```

## Configuration

Configuration files are located in **etc** directory.

Rename the config file for your environment without **sample** extension.

Open the config file and set up **MySQL** configuration:

```php
'model' => [
    'default' => [
        'db_host'       => 'localhost',
        'db_username'   => 'username',
        'db_password'   => 'password',
        'db_database'   => 'database',
        'lc_time_names' => 'en_US',
    ],
],
```

Config file is determined by the **ENVIRONMENT** environment variable (dev, preprod, default).

Others default configurations can be updated in config file (secure, session, cookie, languages, cache, mail...).

## Docroot to improve security

The Leafiny app is served from **pub**. The rest of the Leafiny file system is vulnerable because it is accessible from a browser.
Setting the webroot to the **pub** directory in production prevents site visitors from accessing sensitive areas of the Leafiny file system from a browser.

Make all static public files available in **pub** directory by running **deploy.php** script:

```php
php deploy.php
```

This script create symlinks to **media** and all **modules** static files.

## Backend

By default backend route is : **/admin/leafiny/**

It is recommended to update the backend key (leafiny) in config file.

The default backend account is :

* **Username** : admin
* **Password** : admin

You need tu update credentials on first connexion.

# Tools

**Frontend**

* [Pure CSS](https://github.com/pure-css/pure): Responsive CSS modules
* [Showdown](https://github.com/showdownjs/showdown): A bidirectional Markdown converter (admin)
* *What you want:* You can use your favorite tools and frameworks. Default theme is light and rendered with Pure CSS and Vanilla JS.

**PHP libraries**

* [Twig](https://github.com/twigphp/Twig): Template language for PHP
* [PHPMailer](https://github.com/PHPMailer/PHPMailer): Email sending library
* [MysqliDb](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class): Database statements