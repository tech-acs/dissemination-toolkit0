## Installation

```bash
composer create-project uneca/dissemination-toolkit your-project-name
```

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


### Start development server

```php artisan serve```

Once you have started the Artisan development server, your application will be accessible in your web browser at http://localhost:8000. 
Next, you're ready to start taking your next steps into creating your data dissemination website.

Some of the things you will have to do are:

- Create dimensions
- Create indicators
- Import geographic areas
- Create/import dimension values
- Create and import datasets
