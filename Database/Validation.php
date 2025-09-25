<?php
function validateRegistration($username, $email, $password) {
    $errors = [];

    if (empty($username)) {
        $errors['username'] = "Username must be filled";
    } 
    elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors['username'] = "Username must be between 3-30 characters";
    }

    if (empty($email)) {
        $errors['email'] = "Email must be filled";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } 
    else {
        $atCount = substr_count($email, '@');
        $dotCount = substr_count($email, '.');
        
        if ($atCount !== 1) {
            $errors['email'] = "Email must contain exactly one @";
        }
        if (strpos($email, '@.') !== false || strpos($email, '.@') !== false || 
            strpos($email, '@@') !== false || strpos($email, '..') !== false) {
            $errors['email'] = "Email contains invalid character sequences";
        }
        
        if ($email[0] === '@' || $email[0] === '.' || 
            $email[strlen($email)-1] === '@' || $email[strlen($email)-1] === '.') {
            $errors['email'] = "Email cannot start or end with @ or .";
        }
    }
    
    if (empty($password)) {
        $errors['password'] = "Password must be filled";
    }
    elseif (strlen($password) < 5 || strlen($password) > 90) {
        $errors['password'] = "Password must be between 5-90 characters";
    }
    
    return $errors;
}
function validateLogin($username, $password) {
    $errors = [];
    
    if (empty($username)) {
        $errors['username'] = "Username must be filled";
    }
    if (empty($password)) {
        $errors['password'] = "Password must be filled";
    }
    return $errors;
}

function validatePost($title, $category, $description, $thumbnail = null, $isEdit = false) {
    $errors = [];

    if (empty($title)) {
        $errors['title'] = "Title is required";
    }
    elseif (strlen($title) < 5 || strlen($title) > 75) {
        $errors['title'] = "Title must be between 5-75 characters";
    }

    if (empty($category)) {
        $errors['category'] = "Category is required";
    }

    if (empty($description)) {
        $errors['description'] = "Description is required";
    } 
    elseif (strlen($description) < 15 || strlen($description) > 250) {
        $errors['description'] = "Description must be between 15-250 characters";
    }

    if (!$isEdit && (!$thumbnail || $thumbnail['error'] === UPLOAD_ERR_NO_FILE)) {
        $errors['thumbnail'] = "Thumbnail is required";
    } 
    elseif ($thumbnail && $thumbnail['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 150 * 1024 * 1024;
        
        if (!in_array($thumbnail['type'], $allowedTypes)) {
            $errors['thumbnail'] = "Thumbnail must be JPG, PNG, GIF, or WebP";
        }
        elseif ($thumbnail['size'] > $maxSize) {
            $errors['thumbnail'] = "Thumbnail must be less than 150 MB";
        }
    }
    
    return $errors;
}

function validateReport($reason) {
    $errors = [];
    
    if (empty($reason)) {
        $errors['reason'] = "Report reason is required";
    } elseif (strlen($reason) < 15 || strlen($reason) > 250) {
        $errors['reason'] = "Report reason must be between 15-250 characters";
    }
    
    return $errors;
}

function validateComment($commentText) {
    $errors = [];
    
    if (empty(trim($commentText))) {
        $errors['comment'] = "Comment cannot be empty";
    } 
    elseif (strlen($commentText) > 1000) {
        $errors['comment'] = "Comment must be less than 1000 characters";
    }
    return $errors;
}

function validateCategory($categoryName) {
    $errors = [];
    
    if (empty($categoryName)) {
        $errors['category_name'] = "Category name is required";
    } 
    elseif (strlen($categoryName) < 3 || strlen($categoryName) > 20) {
        $errors['category_name'] = "Category name must be between 3-20 characters";
    }
    
    return $errors;
}

function generateUserID($pdo) {
    do {
        $number=str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $userID= 'U' . $number;
        
        $stmt=$pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $stmt->execute([$userID]);
    }while ($stmt->fetch());
    
    return $userID;
}

function generatePostID($pdo) {
    do{
        $number = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $postID = 'P' . $number;
        
        $stmt = $pdo->prepare("SELECT post_id FROM posts WHERE post_id = ?");
        $stmt->execute([$postID]);
    }while ($stmt->fetch());
    
    return $postID;
}

function determineUserRole($email) {
    return (substr($email, -4) === '.vin') ? 'admin' : 'crafter';
}

function isEmailUnique($pdo, $email, $currentUserID = null) {
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $params = [$email];
    
    if($currentUserID) {
        $sql .= " AND user_id != ?";
        $params[] = $currentUserID;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return !$stmt->fetch();
}

function isUsernameUnique($pdo, $username, $currentUserID = null) {
    $sql = "SELECT user_id FROM users WHERE username = ?";
    $params = [$username];
    
    if ($currentUserID) {
        $sql .= " AND user_id != ?";
        $params[] = $currentUserID;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return !$stmt->fetch();
}
?>
