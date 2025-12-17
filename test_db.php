<?php
$mysqli = @new mysqli('localhost', 'root', '');
if ($mysqli->connect_errno) {
    echo "Koneksi MySQL gagal: " . $mysqli->connect_error;
} else {
    echo "Koneksi MySQL berhasil!\n";
    
    // Test database creation
    $mysqli->query("CREATE DATABASE IF NOT EXISTS chatbot CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $mysqli->select_db('chatbot');
    
    // Test table creation
    $mysqli->query("CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))");
    echo "Database dan tabel berhasil dibuat/ditemukan!\n";
    
    $mysqli->close();
}
?>
