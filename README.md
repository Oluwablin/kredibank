
## Maker-Checker for Kredi Bank Assessment

## Project Description

An administrative system that makes use of maker-checker rules for creating, updating and deleting users.

## Project Setup

### Cloning the GitHub Repository

Clone the repository to your local machine by running the terminal command below.

```bash
git clone https://github.com/Oluwablin/kredibank
```

### Setup Database

Create a MySQL database and note down the required connection parameters. (DB Host, Username, Password, Name)

### Install Composer Dependencies

Navigate to the project root directory via terminal and run the following command.

```bash
composer install
```

### Create a copy of your .env file

Run the following command

```bash
cp .env.example .env
```

This should create an exact copy of the .env.example file. Name the newly created file .env and update it with your local environment variables (database connection info and others).

### Generate an app encryption key

```bash
php artisan key:generate
```
- Create database ```kredibank```

### Migrate the database

```bash
php artisan migrate
```

### Run the database seeds

```bash
php artisan db:seed
```

### To test
- Create database ```kredibank_test```

- Run the following 

```bash
php artisan migrate --env=test
```


```bash
php artisan test --env=test
```

### Api Documentation