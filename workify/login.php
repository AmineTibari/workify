<?php

$host = "localhost";
$dbname = "workify";
$username = "root";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    try {
        $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST["email"];
        $password = $_POST["password"];
        $sql = "SELECT password FROM users WHERE email = :email";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user_password = $stmt->fetchColumn();

            if (password_verify($password, $user_password)) {


                $_SESSION["email"] = $email;
                header("location: dashboard.php");
                exit();

            } else {
                echo "password not correct";
            }
        } else {
            echo "no user found with this email";
        }

    } catch(PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>

