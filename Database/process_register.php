<?php
session_start();
require_once 'database.php';
require_once 'Validation.php';

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $username=trim($_POST['Username']);
    $email=trim($_POST['Email']);
    $password=$_POST['Password'];
    $confirm_password=$_POST['ConfirmPassword'];
    $errors=[];
    if ($password!==$confirm_password) {
        $errors['password']="Passwords do not match";
    }
    $validationErrors=validateRegistration($username, $email, $password);
    $errors=array_merge($errors, $validationErrors);
    if (empty($errors)) {
        $database=new Database();
        $db=$database->getConnection();
        $check_query="SELECT user_id FROM users WHERE username=? OR email=?";
        $check_stmt=$db->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result=$check_stmt->get_result();

        if ($check_result->num_rows>0) {
            $errors['general']="Username or email already exists";
        } else {
            $user_id=generateUserID($db);
            $role=determineUserRole($email);
            $hashed_password=password_hash($password, PASSWORD_DEFAULT);
            $query="INSERT INTO users (user_id, username, email, password, role) VALUES (?, ?, ?, ?, ?)";
            
            $stmt=$db->prepare($query);
            $stmt->bind_param("sssss", $user_id, $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message'=>'Registration successful! Redirecting to login page...','redirect' => '../html/Login.html']);
                exit();
            } else {
                $errors['general']="Registration failed. Please try again.";
            }
            
            $stmt->close();
        }   
        $check_stmt->close();
        $db->close();
    }
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
}
?>
