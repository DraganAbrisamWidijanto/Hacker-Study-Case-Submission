<?php
header("Content-Type: application/json; charset=UTF-8");
include 'db.php';

// Ambil metode dan URI permintaan
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0]; // Ambil path tanpa query strings

// Definisikan struktur endpoint
$baseEndpoint = '/book-api/api.php/api/books';

// Fungsi untuk menangani routing dasar
if ($requestUri === $baseEndpoint) {
    // GET: Membaca semua buku
    if ($requestMethod === 'GET') {
        $result = $conn->query("SELECT * FROM books");

        if ($result->num_rows > 0) {
            $books = $result->fetch_all(MYSQLI_ASSOC);
            $response = [
                "data" => array_map(function($book) {
                    return [
                        "id" => (int)$book['id'],
                        "title" => $book['title'],
                        "author" => $book['author'],
                        "published_at" => $book['published_date'],
                        "created_at" => $book['created_at'],
                        "updated_at" => $book['updated_at']
                    ];
                }, $books)
            ];
            echo json_encode($response);
            http_response_code(200);
            exit; // Stop execution after response
        } else {
            echo json_encode(["data" => [], "message" => "No books found."]);
            http_response_code(404);
            exit; // Stop execution after response
        }
    }
    // POST: Membuat buku baru
    elseif ($requestMethod === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $title = $data->title ?? '';
        $author = $data->author ?? '';
        $published_date = $data->published_date ?? '';
    
        $stmt = $conn->prepare("INSERT INTO books (title, author, published_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $author, $published_date);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            http_response_code(201);
            echo json_encode([
                "message" => "Book created successfully.",
                "data" => [
                    "id" => $stmt->insert_id,
                    "title" => $title,
                    "author" => $author,
                    "published_at" => $published_date,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Failed to create book."]);
        }
        $stmt->close();
        exit; // Stop execution after response
    }
    else {
        echo json_encode(["message" => "Method not allowed."]);
        http_response_code(405); // Method not allowed
        exit; // Stop execution after response
    }
}
elseif (preg_match('/^\/book-api\/api.php\/api\/books\/(\d+)$/', $requestUri, $matches)) {
    $bookId = intval($matches[1]);

    // GET: Membaca satu buku
    if ($requestMethod === 'GET') {
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $response = [
                "message" => "Book found.",
                "data" => [
                    "id" => (int)$book['id'],
                    "title" => $book['title'],
                    "author" => $book['author'],
                    "published_at" => $book['published_date'],
                    "created_at" => $book['created_at'],
                    "updated_at" => $book['updated_at']
                ]
            ];
            echo json_encode($response);
            http_response_code(200);
            exit; // Stop execution after response
        } else {
            echo json_encode(["message" => "Book not found."]);
            http_response_code(404);
            exit; // Stop execution after response
        }
        $stmt->close();
    }
    // PUT: Update an existing book
    elseif ($requestMethod === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));

        // Ambil data yang ada dari input
        $title = isset($data->title) ? $data->title : null;
        $author = isset($data->author) ? $data->author : null;
        $published_date = isset($data->published_date) ? $data->published_date : null;

        // Ambil data buku yang ada dari database untuk mendapatkan nilai saat ini
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        if (!$book) {
            http_response_code(404);
            echo json_encode(["message" => "Book not found."]);
            exit; // Stop execution after response
        }

        // Hanya memperbarui field yang diberikan
        $updatedTitle = $title ? $title : $book['title'];
        $updatedAuthor = $author ? $author : $book['author'];
        $updatedPublishedDate = $published_date ? $published_date : $book['published_date'];

        // Persiapkan dan eksekusi pernyataan update
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, published_date = ? WHERE id = ?");
        $stmt->bind_param("sssi", $updatedTitle, $updatedAuthor, $updatedPublishedDate, $bookId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $updatedBook = [
                    "id" => $bookId,
                    "title" => $updatedTitle,
                    "author" => $updatedAuthor,
                    "published_at" => $updatedPublishedDate,
                    "created_at" => $book['created_at'], // Tetap gunakan nilai yang ada
                    "updated_at" => date('Y-m-d H:i:s'), // Update timestamp
                ];

                echo json_encode([
                    "message" => "Book updated.",
                    "data" => $updatedBook
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No changes made or book not found."]);
            }
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Update failed."]);
        }
        $stmt->close();
        exit; // Stop execution after response
    }

    // DELETE: Menghapus buku
    elseif ($requestMethod === 'DELETE') {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Book deleted."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Book not found."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Delete failed."]);
        }
        $stmt->close();
        exit; // Stop execution after response
    }
    else {
        echo json_encode(["message" => "Method not allowed."]);
        http_response_code(405); // Method not allowed
        exit; // Stop execution after response
    }
}
else {
    echo json_encode(["message" => "Endpoint not found."]);
    http_response_code(404); // Not found
    exit; // Stop execution after response
}

$conn->close();
?>
