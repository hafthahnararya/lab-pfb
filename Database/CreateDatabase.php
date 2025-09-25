<?php

function createVincraftDatabase() {
    $host = 'localhost';
    $dbname = 'Vincraft';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                user_id VARCHAR(6) PRIMARY KEY,
                username VARCHAR(30) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'crafter') DEFAULT 'crafter',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                category_id INT PRIMARY KEY AUTO_INCREMENT,
                category_name VARCHAR(20) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $pdo->exec("
            INSERT IGNORE INTO categories (category_name) VALUES 
            ('Builds'),
            ('Redstone'),
            ('News'),
            ('Tutorial'),
            ('Mods')
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS posts (
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
            )
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS comments (
                comment_id INT PRIMARY KEY AUTO_INCREMENT,
                post_id VARCHAR(6) NOT NULL,
                user_id VARCHAR(6) NOT NULL,
                comment_text TEXT NOT NULL,
                comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            )
        ");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS likes (
                like_id INT PRIMARY KEY AUTO_INCREMENT,
                post_id VARCHAR(6) NOT NULL,
                user_id VARCHAR(6) NOT NULL,
                like_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                UNIQUE KEY unique_like (post_id, user_id)
            )
        ");


        $pdo->exec("
            CREATE TABLE IF NOT EXISTS resource_files (
                file_id INT PRIMARY KEY AUTO_INCREMENT,
                post_id VARCHAR(6) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(50) NOT NULL,
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
            )
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS reports (
                report_id INT PRIMARY KEY AUTO_INCREMENT,
                post_id VARCHAR(6) NOT NULL,
                reporter_user_id VARCHAR(6) NOT NULL,
                reason VARCHAR(250) NOT NULL,
                report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('pending', 'resolved') DEFAULT 'pending',
                FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
                FOREIGN KEY (reporter_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                UNIQUE KEY unique_report (post_id, reporter_user_id)
            )
        ");
        
        return true;
        
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

if (isset($_GET['setup']) && $_GET['setup'] === 'true') {
    createVincraftDatabase();
} else {
    echo "<h2>VinCraft Database Setup</h2>";
    echo "<p>This script will create the VinCraft database and all necessary tables.</p>";
    echo "<p><a href='?setup=true' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Click here to run database setup</a></p>";
    echo "<p><strong>Warning:</strong> This will create the database structure. Make sure your database credentials are correct.</p>";
}
?>
