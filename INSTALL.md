## Deployment instructions

The first step is to clone this repository and cd into the directory. Then run the following
commands.

### Install all required PHP packages

```composer install```


### Set environment variables

Edit the .env file and set the postgreSQL database credentials and other relevant settings

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dissemination-db
DB_USERNAME=postgres-user
DB_PASSWORD=postgres-password
```

### Run database migrations and seed database

```php artisan migrate --seed```

### Create admin account

```php artisan adminify```

### Seed with data
Login with the admin account just created and seed the application with basic data

- Area hierarchies
- Areas
- Topics
- Indicators
- Dimensions (also create the dimension tables)
- Dimension values
- Datasets
- Import datasets

### Create content
- Visualizations 
- Stories
