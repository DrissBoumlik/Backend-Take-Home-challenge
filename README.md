# News Aggregator Backend

This project implements the backend functionality for a news aggregator. It fetches articles from various sources, stores them in a database, and provides APIs for the frontend to query and filter articles.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Project Structure](#project-structure)
3. [Setup and Run the App](#setup-and-run-the-app)
4. [Import Articles from Sources](#import-articles-from-sources)
    - [Scheduled Import](#scheduled-import)
    - [Manual Import](#manual-import)
5. [Run Tests](#run-tests)
6. [API Endpoints](#api-endpoints)

---

## Requirements

- **PHP**: Version 8.1
- **Composer**: Latest version
- **Database**: MySQL/SQLite

---

## Project Structure


Note: If you would like to go back to the default laravel structure run:

```bash
git checkout laravel_default_structure
```

```
app
├── Config                      # Contains Configuration classes (pagination & ttl for caching)
├── Console                     # Registers artisan commands from src/ directory
├── Contracts                   # Shared interfaces and contracts
├── Services                    # Application-wide services

src
├── Domain                      # Domain-specific layers
│   ├── Articles                # Handles articles-related logic
│   │   ├── database/factories  # Used in testing
│   │   ├── Exceptions          # Custom exceptions for articles
│   │   ├── Http
│   │   │   ├── Controllers     # Handles article-related functionality
│   │   │   ├── Requests        # Form request validations for articles
│   │   │   └── Resources       # API resource transformations for articles
│   │   ├── Models              # Articles eloquent models
│   │   └── Services            # Business logic services for articles
│   ├── NewsApis                # Handles news API integrations
│   │   ├── Console             # Artisan command for importing articles from apis sources
│   │   ├── Contracts           # Interface definitions for APIs
│   │   └── Services            # Services for news APIs
│   │   │   └── Sources         # Specific source-related service logic
│   └── Users                   # Handles user-related functionality
│   │   ├── database/factories  # Used in testing
│   │   ├── Http
│   │   │   ├── Controllers     # Handles user-related functionality (preferences)
│   │   │   └── Resources       # User preferences resource transformations
│   │   ├── Models              # User & User preferences eloquent models
│   │   └── Services            # Business logic services for user preferences
```

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

- Start the scheduler using a cron job:
    - Add the appropriate command to your crontab.

        ```bash
        * * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
        ```
    - Or by running:
        ```bash
        php artisan schedule:run
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
