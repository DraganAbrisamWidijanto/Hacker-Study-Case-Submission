### API Documentation: Book Management

**Base URL**: `http://localhost/book-api/api.php`

---

## Environment Setup
This API has been developed using the following technologies:

- **Web Server**: XAMPP
  - **Version**: (8.2.0)
- **Database**: MariaDB
  - **Version**: (10.4.27)
- **Programming Language**: PHP
  - **Version**: (8.2.0)
  
### Database Configuration
1. **Database Name**: `book_management`
2. **Table Structure**: The main table for this API is `books`, with the following fields:
   - `id` (INT, Primary Key, Auto Increment)
   - `title` (VARCHAR)
   - `author` (VARCHAR)
   - `published_at` (DATE)
   - `created_at` (TIMESTAMP)
   - `updated_at` (TIMESTAMP)

### Installation Instructions
1. Install XAMPP from [Apache Friends](https://www.apachefriends.org/index.html).
2. Start the XAMPP control panel and run Apache and MySQL.
3. Import the `bookdb.sql` code in PHPMyAdmin to create the necessary database and tables.
4. Place the PHP files serving the API endpoints in the `htdocs` directory of your XAMPP installation, for example: `C:\xampp\htdocs\book-api`.

---
## API Usage with Postman
To use the API with Postman, you can define the base URL as a variable. 

- **Variable**: `{{APP_URL}}`
- **Value**: `http://localhost/book-api/api.php`

## API Endpoints

### 1. Get All Books
- **Endpoint**: `/books`
- **Method**: `GET`
- **Description**: Retrieves a list of all books in the database.
- **Responses**:
  - **200 OK**
    ```json
    {
      "data": [
        {
          "id": 1,
          "title": "Sample Book Title",
          "author": "Author Name",
          "published_at": "YYYY-MM-DD",
          "created_at": "YYYY-MM-DD HH:MM:SS",
          "updated_at": "YYYY-MM-DD HH:MM:SS"
        }
      ]
    }
    ```
  - **404 Not Found**
    ```json
    {
      "data": [],
      "message": "No books found."
    }
    ```

---

### 2. Create Book
- **Endpoint**: `/books`
- **Method**: `POST`
- **Description**: Creates a new book in the database.
- **Request Body**:
  ```json
  {
    "title": "Book Title",
    "author": "Author Name",
    "published_date": "YYYY-MM-DD"
  }
  ```
- **Responses**:
  - **201 Created**
    ```json
    {
      "message": "Book created successfully.",
      "data": {
        "id": 2,
        "title": "Book Title",
        "author": "Author Name",
        "published_at": "YYYY-MM-DD",
        "created_at": "YYYY-MM-DD HH:MM:SS",
        "updated_at": "YYYY-MM-DD HH:MM:SS"
      }
    }
    ```
  - **400 Bad Request**
    ```json
    {
      "message": "Failed to create book."
    }
    ```

---

### 3. Get Single Book
- **Endpoint**: `/books/{id}`
- **Method**: `GET`
- **Description**: Retrieves a specific book by its ID.
- **Responses**:
  - **200 OK**
    ```json
    {
      "message": "Book found.",
      "data": {
        "id": 1,
        "title": "Book Title",
        "author": "Author Name",
        "published_at": "YYYY-MM-DD",
        "created_at": "YYYY-MM-DD HH:MM:SS",
        "updated_at": "YYYY-MM-DD HH:MM:SS"
      }
    }
    ```
  - **404 Not Found**
    ```json
    {
      "message": "Book not found."
    }
    ```

---

### 4. Update Book
- **Endpoint**: `/books/{id}`
- **Method**: `PUT`
- **Description**: Updates an existing book's details.
- **Request Body**:
  ```json
  {
    "title": "Updated Book Title",
    "author": "Updated Author Name",
    "published_date": "YYYY-MM-DD"
  }
  ```
- **Responses**:
  - **200 OK**
    ```json
    {
      "message": "Book updated.",
      "data": {
        "id": 1,
        "title": "Updated Book Title",
        "author": "Updated Author Name",
        "published_at": "YYYY-MM-DD",
        "created_at": "YYYY-MM-DD HH:MM:SS",
        "updated_at": "YYYY-MM-DD HH:MM:SS"
      }
    }
    ```
  - **404 Not Found**
    ```json
    {
      "message": "Book not found."
    }
    ```
  - **500 Internal Server Error**
    ```json
    {
      "message": "Update failed."
    }
    ```

---

### 5. Delete Book
- **Endpoint**: `/books/{id}`
- **Method**: `DELETE`
- **Description**: Deletes a specific book by its ID.
- **Responses**:
  - **200 OK**
    ```json
    {
      "message": "Book deleted."
    }
    ```
  - **404 Not Found**
    ```json
    {
      "message": "Book not found."
    }
    ```
  - **500 Internal Server Error**
    ```json
    {
      "message": "Delete failed."
    }
    ```
