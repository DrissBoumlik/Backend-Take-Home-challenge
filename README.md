# News Aggregator Backend

This project implements the backend functionality for a news aggregator. It fetches articles from various sources, stores them in a database, and provides APIs for the frontend to query and filter articles.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Setup and Run the App](#setup-and-run-the-app)
3. [Import Articles from Sources](#import-articles-from-sources)
    - [Scheduled Import](#scheduled-import)
    - [Manual Import](#manual-import)
4. [Run Tests](#run-tests)
5. [API Endpoints](#api-endpoints)

---

## Requirements

- **PHP**: Version 8.1
- **Composer**: Latest version
- **Database**: MySQL/SQLite

---

## Setup and Run the App

1. Clone the repository and navigate to the project folder.

    ```bash
    git clone https://github.com/drissboumlik/Backend-Take-Home-challenge/
    cd Backend-Take-Home-challenge
    ```

2. Install dependencies using Composer.

    ```bash
    composer install
    ```

3. Configure environment variables:
    - Copy `.env.example` to `.env`.
        ```bash
        cp .env.example .env
        ```
    - Create the file database\database.sqlite if you want to use sqlite
    - Run 
        ```bash 
        php artisan key:generate
        ```
    - Update the `.env` file with your database credentials and API keys.


4. Run database migrations.

    ```bash
    php artisan migrate
    ```

5. Start the application.

    ```bash
    php artisan serve
    ```

    The app will be available at `http://127.0.0.1:8000`.

---

## Import Articles from Sources

### Scheduled Import

To automatically import articles hourly, set up a Laravel scheduler:

1. Open `app/Console/Kernel.php` and ensure the scheduled task is defined.

    ```php
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('articles:import')->hourly();
    }
    ```

2. Start the scheduler using a cron job:
    - Add the appropriate command to your crontab.

        ```bash
        * * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
        ```


### Manual Import

Run the command to fetch articles manually.
```bash
php artisan articles:import
```

---

## Run Tests

Run the project's test suite to ensure all functionalities work as expected.

```bash
php artisan test
```

```bash
php artisan test --parallel --coverage --min=80
```

---

## API Endpoints

The following API endpoints are available:

### Fetch All Articles

- **Endpoint:** `GET /api/v1/articles`  
- **Description:** Retrieve all articles, with pagination support.  
- **Parameters:**
  - `per_page` (optional): Number of articles per page.

### Search Articles
- **Endpoint:** `GET /api/v1/articles/search?term=news`  
- **Description:** Search for articles containing the specified term in their title or content.  
- **Parameter:**
  - `term`: The search term to filter articles.  
  - `per_page` (optional): Number of articles per page.

### Filter Articles
- **Endpoint:** `GET /api/v1/articles/filter?start_date=2025-01-01&end_date=2025-01-10`  
- **Description:** Filter articles based on specific criteria.  
- **Parameters:**
  - `category` (optional): Filter by article category.
  - `source` (optional): Filter by source of the article.
  - `start_date` (optional): Filter by articles published after this date (YYYY-MM-DD).
  - `end_date` (optional): Filter by articles published before this date (YYYY-MM-DD).
  - `per_page` (optional): Number of articles per page.

### User Preferences for Articles
- **Endpoint:** `GET /api/v1/articles/preferences`  
- **Description:** Retrieve articles tailored to the user's preferences, such as selected categories or sources.  
- **Parameter:**
  - `per_page` (optional): Number of articles per page.

### Manage User Preferences
- **Endpoint:** `GET /api/v1/user/preferences`  
- **Description:** Retrieve user preferences for filtering articles.  
- **Parameter:**
    - `per_page` (optional): Number of articles per page.
    - Rest of filters are fetched from the user_preferences table
