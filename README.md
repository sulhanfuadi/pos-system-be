## About This Project

This project is a Point of Sale (POS) system built using the Laravel framework. It leverages Laravel's robust features to provide a seamless and efficient POS experience.

## Features

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/sulhanfuadi/pos-system-be.git
    cd pos-system-be
    ```

2. Install dependencies:

    ```sh
    composer install
    npm install
    ```

3. Copy the example environment file and configure the environment variables:

    ```sh
    cp .env.example .env
    ```

4. Generate the application key:

    ```sh
    php artisan key:generate
    ```

5. Run the database migrations:

    ```sh
    php artisan migrate
    ```

6. Seed the database with initial data:

    ```sh
    php artisan db:seed
    ```

7. Start the development server:
    ```sh
    php artisan serve
    ```

## Usage

To access the application, open your browser and navigate to `http://localhost:8000`.

## Testing

To run the tests, use the following command:

```sh
phpunit
```

## Contributing

Thank you for considering contributing to the project! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
