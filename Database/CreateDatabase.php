<?php
function createVincraftDatabase() {
    $host = 'localhost';
    $dbname = 'Vincraft';
    $username = 'root';
    $password = '';
    $conn = new mysqli($host, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists<br>";
    } else {
        die("Error creating database: " . $conn->error);
    }
    $conn->select_db($dbname);
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id VARCHAR(6) PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'crafter') DEFAULT 'crafter',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'users' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        category_id INT PRIMARY KEY AUTO_INCREMENT,
        category_name VARCHAR(20) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'categories' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    $sql = "INSERT IGNORE INTO categories (category_name) VALUES 
        ('Builds'),
        ('Redstone'),
        ('News'),
        ('Tutorial'),
        ('Mods')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Default categories inserted successfully<br>";
    } else {
        echo "Error inserting categories: " . $conn->error . "<br>";
    }
    
    // Create posts table
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        post_id VARCHAR(6) PRIMARY KEY,
        user_id VARCHAR(6) NOT NULL,
        category_id INT NOT NULL,
        title VARCHAR(75) NOT NULL,
        description VARCHAR(250) NOT NULL,
        video_link VARCHAR(255),
        thumbnail_path VARCHAR(500),
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'posts' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    
    // Create comments table
    $sql = "CREATE TABLE IF NOT EXISTS comments (
        comment_id INT PRIMARY KEY AUTO_INCREMENT,
        post_id VARCHAR(6) NOT NULL,
        user_id VARCHAR(6) NOT NULL,
        comment_text TEXT NOT NULL,
        comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'comments' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    
    // Create likes table
    $sql = "CREATE TABLE IF NOT EXISTS likes (
        like_id INT PRIMARY KEY AUTO_INCREMENT,
        post_id VARCHAR(6) NOT NULL,
        user_id VARCHAR(6) NOT NULL,
        like_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        UNIQUE KEY unique_like (post_id, user_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'likes' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    
    // Create resource_files table
    $sql = "CREATE TABLE IF NOT EXISTS resource_files (
        file_id INT PRIMARY KEY AUTO_INCREMENT,
        post_id VARCHAR(6) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_type VARCHAR(50) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'resource_files' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    
    // Create reports table
    $sql = "CREATE TABLE IF NOT EXISTS reports (
        report_id INT PRIMARY KEY AUTO_INCREMENT,
        post_id VARCHAR(6) NOT NULL,
        reporter_user_id VARCHAR(6) NOT NULL,
        reason VARCHAR(250) NOT NULL,
        report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'resolved') DEFAULT 'pending',
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
        FOREIGN KEY (reporter_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        UNIQUE KEY unique_report (post_id, reporter_user_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'reports' created successfully<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }
    
    $conn->close();
    return true;
}

if (isset($_GET['setup']) && $_GET['setup'] === 'true') {
    createVincraftDatabase();
    echo "<h2>Database setup completed!</h2>";
} else {
    echo "<h2>VinCraft Database Setup</h2>";
    echo "<p>This script will create the VinCraft database and all necessary tables.</p>";
    echo "<p><a href='?setup=true' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Click here to run database setup</a></p>";
    echo "<p><strong>Warning:</strong> This will create the database structure. Make sure your database credentials are correct.</p>";
}
?>
