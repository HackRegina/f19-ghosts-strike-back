# About

Repository for the OctoberCMS-powered needles.pewdiepie.ca.
Active development in develop branch; features in feature branches.
Develop locally using Homestead.
Pushes to develop are automatically deployed on the [dev server](https://needles.pewdiepie.ca/).

## Setup

1. Clone the repo
2. Copy .env.example to .env and configure accordingly (APP_URL and DB credentials)
3. Run the deployment script below, with the exception that `php artisan key:generate` needs to be run after `composer install`

## Deployment Script
```shell
# Make the storage directories if they don't already exsit
mkdir -p $SHARED/storage/app/media &&
mkdir -p $SHARED/storage/app/uploads/public &&
mkdir -p $SHARED/storage/cms/cache &&
mkdir -p $SHARED/storage/cms/combiner &&
mkdir -p $SHARED/storage/cms/twig &&
mkdir -p $SHARED/storage/framework/cache &&
mkdir -p $SHARED/storage/framework/sessions &&
mkdir -p $SHARED/storage/framework/views &&
mkdir -p $SHARED/storage/temp/public &&
mkdir -p $SHARED/storage/logs &&
mkdir -p $SHARED/storage/temp/public &&

# Remove the current storage directory
rm -rf $RELEASE/storage &&

# Link the application storage directory to this
ln -s $SHARED/storage $RELEASE/storage &&

# Install dependencies from composer.lock - add --no-dev for staging & production environments
composer install &&

# Enable maintenance mode
php artisan down &&

# Run any pending migrations
php artisan october:up &&

# Remove and regenerate the symlinked public directory for whitelist approach to clean out
# any references that may have been removed and add any new ones that may have been added
rm -rf public &&
php artisan october:mirror public --relative &&

# Disable maintenance mode
php artisan up;
```