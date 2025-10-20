<?php
session_start();
require_once'database.php';
require_once'Validation.php';

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $username=trim($_POST['Username']);
    $password=$_POST['Password'];
    $remember_me=isset($_POST['RememberMe']) ? true : false;
    $errors=validateLogin($username, $password);
    if (empty($errors)){
        $database=new Database();
        $db=$database->getConnection();
        $query ="SELECT user_id, username, email, password, role FROM users WHERE username=?";
        $stmt=$db->prepare($query);
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $result=$stmt->get_result();
        if ($result->num_rows == 1) {
            $user=$result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']=$user['user_id'];
                $_SESSION['username']=$user['username'];
                $_SESSION['email']=$user['email'];
                $_SESSION['role']=$user['role'];
                $_SESSION['loggedin']=true;
                if ($remember_me) {
                    $cookie_name ="remember_username";
                    $cookie_value=$username;
                    setcookie($cookie_name, $cookie_value, time() + (14 * 24 * 60 * 60),"/"); // 2 weeks
                }
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' =>'Login successful!','redirect' =>'../html/Homepage.html'
                ]);
                exit();
            } 
            else{
                $errors['general'] ="Invalid username or password";
            }
        } else {
            $errors['general'] ="Invalid username or password";
        }
        
        $stmt->close();
        $db->close();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false,'errors' => $errors]);
    exit();
}
?>
