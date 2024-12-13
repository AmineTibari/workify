<?php
session_start();

$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;

// الاتصال بقاعدة البيانات
try {
    $host = "localhost";
    $dbname = "workify";
    $username = "root";
    $password = "";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إضافة لوحة جديدة
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $board_name = isset($_POST['board_name']) ? trim($_POST['board_name']) : '';

        if (!empty($board_name)) {
            // استعلام لإضافة اللوحة
            $stmt_add_board = $pdo->prepare("INSERT INTO boards (board_name, email) VALUES (?, ?)");
            $stmt_add_board->execute([$board_name, $email]);
            header("Location: dashboard.php"); // إعادة توجيه إلى صفحة عرض اللوحات بعد إضافة اللوحة
            exit();
        } else {
            echo "<p style='color:red;'>يرجى إدخال اسم اللوحة.</p>";
        }
    }
} catch (PDOException $e) {
    echo "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ajouter une board - workify</title>
    <link rel="stylesheet" href="css/addboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <a href="dashboard.php"><span id="logo">workify</span></a>

    <a id="account" href="account.php"><img src="src/user%20(1).png" /></a>

</header>

<div class="container">
    <h2>Ajouter une board</h2>

    <form method="POST" action="addboard.php">
        <div class="form-group">
            <label for="board_name">Nom de la board:</label>
            <input type="text" name="board_name" id="board_name" required>
        </div>

        <div class="form-group">
            <button type="submit">Ajouter la board</button>
        </div>
    </form>
</div>

<footer>
    2024 &copy; tous les droits réservés
</footer>

</body>

</html>
