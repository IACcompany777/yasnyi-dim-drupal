# Yasnyi Dim

A Drupal 11 site concept by IAC for a made-up cleaning company. It is a portfolio example, not a client project.

## What it includes

- Seven pages in Ukrainian and English
- Contact and order forms built with Webform
- Translated menus, page addresses, and forms
- A custom responsive theme in `web/themes/custom/yasnyi`

## Run it locally

You need PHP 8.3 or newer with the SQLite, GD, and mbstring extensions, plus Composer.

```bash
composer install
php vendor/drush/drush/drush.php site:install standard --yes --locale=uk --db-url=sqlite://sites/default/files/.ht.sqlite --site-name="Ясний Дім" --account-name=admin --account-mail=admin@example.com --site-mail=admin@example.com
php vendor/drush/drush/drush.php pm:install -y language locale content_translation config_translation webform webform_ui
php vendor/drush/drush/drush.php php:script scripts/01-page-type.php
php vendor/drush/drush/drush.php php:script scripts/02-content.php
php vendor/drush/drush/drush.php php:script scripts/03-blocks.php
php vendor/drush/drush/drush.php php:script scripts/04-finish.php
php vendor/drush/drush/drush.php cr
```

Then start the site from the `web` folder and open http://127.0.0.1:8888/:

```bash
php -S 127.0.0.1:8888 .ht.router.php
```

Drush prints a generated admin password during installation. Form messages are saved in the site and are not sent by email.
