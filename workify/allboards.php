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

        $stmt_taches = $pdo->prepare("SELECT * FROM taches WHERE status IS NOT NULL AND email = ?");
        $stmt_taches->execute([$email]);
        $result_taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);

        $stmt_boards = $pdo->prepare("
        SELECT boards.board_name, 
        COUNT(CASE WHEN taches.status = 0 THEN 1 END) AS task_in_progress,
        COUNT(CASE WHEN taches.status = 1 THEN 1 END) AS task_completed
        FROM boards
        LEFT JOIN taches ON boards.board_name = taches.board_name
        WHERE boards.email = ?
        GROUP BY boards.board_id;
    ");
        $stmt_boards->execute([$email]);
        $result_boards = $stmt_boards->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Boards - Workify</title>
    <link rel="stylesheet" href="css/allboards.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <a href="dashboard.php"><span id="logo">workify</span></a>

    <a id="account" href="account.php"><img src="src/user%20(1).png" /></a>

</header>

<div class="container">
    <div class="boards">
        <div class="boards-title">
            <h2>Liste des Boards</h2>
            <div>
                <button onclick="window.location.href='addboard.php'">Ajouter une Board</button>
            </div>
        </div>

        <div class="boards-list">
            <?php if (!empty($result_boards)): ?>
                <?php foreach ($result_boards as $row): ?>
                    <div class="board">
                        <span class="board-title"><?php echo htmlspecialchars($row['board_name']); ?></span>
                        <div class="board-tasks">
                            <div>
                                <strong>Tâches en cours:</strong>
                                <?php echo $row['task_in_progress']; ?>
                            </div>
                            <div>
                                <strong>Tâches terminées:</strong>
                                <?php echo $row['task_completed']; ?>
                            </div>
                        </div>
                        <form method="GET" action="board.php">
                            <input type="hidden" name="board_name" value="<?php echo htmlspecialchars($row['board_name']); ?>">
                            <button class="btn-primary" type="submit">Voir</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune Board disponible.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Tous les droits réservés - Workify</p>
</footer>
</body>
</html>
