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

### Seed some sample data

```
php artisan db:seed --class=WorkingDataSeeder
php artisan data:create-dimensions
php artisan db:seed --class=DimensionValueSeeder
```

**Area**

Import the areas. The csv file is located in the sample-data folder located in the root directory.

Finally, create the following datasets and import the data for each one from the respective files

**Namibia population, hhs, avg. hh size for 1991 - 2023 censuses**

- Indicators
    - Average household size
    - Households
    - Population
- Dimensions
    - Year
- Fact table
    - Population facts
- Data geographic granularity
    - Country

**Population by sex and 5 year age group**

- Indicators
    - Population
- Dimensions
    - Five year age group
    - Sex
    - Year
- Fact table
    - Population facts
- Data geographic granularity
    - Country
- Topic
    - Population

**Population density, hh pop, hhs, and avg hh size by constituency, Namibia 2023 census**

- Indicators
    - Average household size
    - Household population
    - Households
    - Population density
- Dimensions
    - Year
- Fact table
    - Population facts
- Data geographic granularity
    - Constituency
- Topic
    - Population

**Population by Constituency and Sex, Namibia 2023**

- Indicators
    - Population
- Dimensions
    - Year
    - Sex
- Fact table
    - Population facts
- Data geographic granularity
    - Constituency
- Topic
    - Population
