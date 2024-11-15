<?php
   $host = 'localhost'; // Ganti dengan host database Anda
   $db = 'bookdb';      // Nama database
   $user = 'root';      // Nama pengguna database
   $pass = '';          // Password database

   $conn = new mysqli($host, $user, $pass, $db);

   if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
?>
