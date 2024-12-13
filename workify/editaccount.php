<?php


session_start();

$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;

if ($email == null) {
    header("Location: login.html");
    exit;
} else {


    try {
        $host = "localhost";
        $dbname = "workify";
        $username = "root";
        $password = "";
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($_POST["type"] == "username") {
            $change_username = $pdo->prepare("UPDATE users SET username = ? WHERE email = ?");
            $change_username->execute([$_POST["username"], $email]);
            header("location: account.php");
            exit;
        }
        if ($_POST["type"] == "password") {

            $user_password = $_POST["actual-password"];
            $new_password = $_POST["new-password"];

            if (empty($user_password) || empty($new_password)) {
                echo "Please fill in all fields.";
                exit;
            }

            $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $storedPassword = $stmt->fetchColumn();

            if ($storedPassword && password_verify($user_password, $storedPassword)) {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $change_password = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $change_password->execute([$hashedPassword, $email]);

                header("location: account.php");
                exit;

            } else {
                echo "Incorrect current password.";
            }
        }




    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>