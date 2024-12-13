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


        $stmt_boards = $pdo->prepare("SELECT * FROM boards WHERE email = ?");
        $stmt_boards->execute([$email]);
        $result_boards = $stmt_boards->fetchAll(PDO::FETCH_ASSOC);

        // إضافة مهمة جديدة
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tache_title = isset($_POST['tache_title']) ? $_POST['tache_title'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : NULL;
            $board_name = isset($_POST['board_name']) ? $_POST['board_name'] : NULL;
            $finishing_at = !empty($_POST['finishing_at']) ? $_POST['finishing_at'] : NULL;
            $created_at = date("Y-m-d H:i:s");

            // استعلام لإضافة المهمة إلى قاعدة البيانات
            if (!empty($tache_title)) {
                $stmt_add_task = $pdo->prepare("INSERT INTO taches (tache_title, status, board_name, created_at, finishing_at, email) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_add_task->execute([$tache_title, $status, $board_name, $created_at, $finishing_at, $email]);
                header("Location: dashboard.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ajouter une tâche - workify</title>
    <link rel="stylesheet" href="css/addtask.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <a href="dashboard.php"><span id="logo">workify</span></a>

    <a id="account" href="account.php"><img src="src/user%20(1).png" /></a>

</header>

<div class="container">
    <h2>Ajouter une tâche</h2>

    <form method="POST" action="addtask.php">
        <div class="form-group">
            <label for="tache_title">Titre de la tâche:</label>
            <input type="text" name="tache_title" id="tache_title" required>
        </div>

        <div class="form-group">
            <label for="status">Statut:</label>
            <select name="status" id="status">
                <option value="0">En cours</option>
                <option value="1">Terminée</option>
                <option value="null">Annulée</option>
            </select>
        </div>

        <div class="form-group">
            <label for="board_name">Choisir une board:</label>
            <select name="board_name" id="board_name">
                <option value="">Aucune</option>
                <?php foreach ($result_boards as $board): ?>
                    <option value="<?php echo htmlspecialchars($board['board_name']); ?>"><?php echo htmlspecialchars($board['board_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="finishing_at">Date limite:</label>
            <input type="date" name="finishing_at" id="finishing_at">
        </div>

        <div class="form-group">
            <button type="submit">Ajouter la tâche</button>
        </div>
    </form>
</div>

<footer>
    2024 &copy; tous les droits réservés
</footer>

</body>

</html>
