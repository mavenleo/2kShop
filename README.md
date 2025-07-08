# 2kShop

Basic product list and wishlist features built with Laravel.

## Table of Contents

- [Stack Used](#stack-used)
- [Setup](#setup)
    - [Normal Laravel Setup](#normal-laravel-setup)
    - [Docker Setup](#docker-setup)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Running Unit Tests](#running-unit-tests)
- [Project Thoughts & Philosophy](#project-thoughts--philosophy)


## Stack Used

- Laravel 10.x
- PHP 8.1
- MySQL 8
- Vue 3 + Vite + Inertia  #Added UI for login/registration, products and wishlist pages.
- Docker
- Nginx
- Redis

## Setup

### Normal Laravel Setup

1. **Clone the Repository:**
   ```bash
   git clone git@github.com:mavenleo/2kShop.git
   cd 2kShop
   ```
2. **Install Dependencies:**
    ```bash
    composer install
    yarn install
    ```
3. **Copy Environment File:**
    ```bash
    cp .env.example .env
    ```
4. **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```
5. **Run Migrations & Seed:**
    ```bash
    php artisan migrate --seed
    ```
6. **Run Development Server:**
    ```bash
    yarn dev
    php artisan serve
    ```

### Docker Setup

1. **CD to application root:**
   ```bash
   cd 2kShop
   ```
1. **Copy Environment File:**
   ```bash
   cp .env.example .env
   ```
1. **Build and Run Docker Containers:**
   ```bash
   docker-compose up -d --build
   ```
   
## Configuration
**Environment Configuration:**
- Update .env file with your database and other configuration settings.
  
## Running the Application
**Normal Laravel Setup:**
- Access the application at http://localhost:8000

**Docker Setup:**
- Access the application at http://localhost:8080
  
## API Endpoints

### Authentication

#### Register
- **Endpoint:** `POST /api/v1/auth/register`
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Sample Response (201):**
  ```json
  {
    "message": "User registered successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "..."
  }
  ```

#### Login
- **Endpoint:** `POST /api/v1/auth/login`
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Sample Response (200):**
  ```json
  {
    "message": "Login successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
  ```

#### Logout
- **Endpoint:** `POST /api/v1/auth/logout`
- **Authentication:** Required (session)
- **Sample Response (200):**
  ```json
  {
    "message": "Logout successful"
  }
  ```

#### Get Current User
- **Endpoint:** `GET /api/v1/auth/user`
- **Authentication:** Required (session)
- **Sample Response (200):**
  ```json
  {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
  ```

---

### Products

#### List Products
- **Endpoint:** `GET /api/v1/products`
- **Authentication:** Required
- **Sample Response (200):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Pioneer DJ Mixer",
        "price": "699.00",
        "description": "A professional DJ mixer.",
        "is_wishlisted": true
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/v1/products?page=1",
      "last": "http://localhost:8000/api/v1/products?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 1
    }
  }
  ```

#### Get Single Product
- **Endpoint:** `GET /api/v1/products/{id}`
- **Authentication:** Optional
- **Sample Response (200):**
  ```json
  {
    "data": {
      "id": 1,
      "name": "Pioneer DJ Mixer",
      "price": "699.00",
      "description": "A professional DJ mixer."
    },
    "is_wishlisted": true
  }
  ```

---

### Wishlist

> All wishlist endpoints require authentication.

#### Get Wishlist
- **Endpoint:** `GET /api/v1/wishlist`
- **Sample Response (200):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "product": {
          "id": 1,
          "name": "Pioneer DJ Mixer"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 1
    }
  }
  ```

#### Add Product to Wishlist
- **Endpoint:** `POST /api/v1/wishlist`
- **Body:**
  ```json
  {
    "product_id": 1
  }
  ```
- **Sample Response (201):**
  ```json
  {
    "message": "Product added to wishlist successfully",
    "data": {
      "id": 1,
      "user_id": 1,
      "product_id": 1
    },
    "count": 1
  }
  ```

#### Remove Product from Wishlist
- **Endpoint:** `DELETE /api/v1/wishlist`
- **Body:**
  ```json
  {
    "product_id": 1
  }
  ```
- **Sample Response (200):**
  ```json
  {
    "message": "Product removed from wishlist successfully",
    "count": 0
  }
  ```

#### Toggle Product in Wishlist
- **Endpoint:** `POST /api/v1/wishlist/toggle`
- **Body:**
  ```json
  {
    "product_id": 1
  }
  ```
- **Sample Response (200):**
  ```json
  {
    "message": "Product added from wishlist successfully",
    "data": {
      "action": "added",
      "is_in_wishlist": true,
      "count": 1
    }
  }
  ```

#### Check if Product is in Wishlist
- **Endpoint:** `GET /api/v1/wishlist/check/{productId}`
- **Sample Response (200):**
  ```json
  {
    "is_in_wishlist": true
  }
  ```

#### Get Wishlist Count
- **Endpoint:** `GET /api/v1/wishlist/count`
- **Sample Response (200):**
  ```json
  {
    "count": 3
  }
  ```

#### Clear Wishlist
- **Endpoint:** `DELETE /api/v1/wishlist/clear`
- **Sample Response (200):**
  ```json
  {
    "message": "Wishlist cleared successfully",
    "count": 0
  }
  ```

## Running Unit Tests
```shell
php artisan test
```

## Project Thoughts & Philosophy

See [thoughts.md](./thoughts.md) for details on my approach to simplicity, avoiding over-engineering, and areas for future improvement if the app grows.
