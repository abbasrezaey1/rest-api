# Neredataltics symfony rest api

## Installation steps

### 1. Install the packages

This application, you can include the packages with the following command:

```bash
composer install
```
### 2. Add the DB credentials

Next make sure to create a new database and add your database credentials to your .env file:

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/database?serverVersion=8&charset=utf8mb4"
```

### 3. Run Migration
```bash
php bin/console make:migration
```

### 4. Pulling data from API
```bash
php bin/console app:pull-users-posts
```
![image](https://user-images.githubusercontent.com/16781160/218746947-2e06d31a-48ab-4fe9-b5b5-cab68bd797a8.png)


## Run and access application
### Run application
```bash
symfony server:start
```

Ps, Install symfony local server. Source: https://symfony.com/doc/current/setup/symfony_server.html

### Access application
```bash
GET:: http://127.0.0.1:8000/api/users?status=active&gender=male
```
<img width="1300" alt="image" src="https://user-images.githubusercontent.com/16781160/218746528-d172ccc5-3148-4c41-ae08-0641c1e8c5c5.png">

## Notes
- Will run the data pull command by cron job to store new data on database.
  - Will check user and post id with stored id for data integrate.
- Will support API versioning using API request header/URL version structure.
- Will implement cache and API throttling to increase API performance.
- Will write unit tests to maintain codebase.
