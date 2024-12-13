<?php
session_start();

$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;

if ($email == null) {
    header("Location: login.html");
    exit;
} else {

if (!isset($_GET['board_name'])) {
    die("Board non spécifié.");
}

$board_name = $_GET['board_name'];

try {
    $host = "localhost";
    $dbname = "workify";
    $username = "root";
    $password = "";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب المهام المتعلقة باللوحة المحددة
    $stmt_taches = $pdo->prepare("SELECT * FROM taches WHERE board_name = ? AND status IS NOT NULL AND email = ? ORDER BY tache_id DESC;");
    $stmt_taches->execute([$board_name, $email]);
    $result_taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Board - <?php echo htmlspecialchars($board_name); ?> - Workify</title>
    <link rel="stylesheet" href="css/board.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <a href="dashboard.php"><span id="logo">workify</span></a>

    <a id="account" href="account.php"><img src="src/user%20(1).png" /></a>

</header>

<div class="container">

    <div class="board-header">
        <h2>Tableau : <?php echo htmlspecialchars($board_name); ?></h2>

        <a href="addtask.php">
            <button>Ajouter une tâche</button>
        </a>


    </div>

    <div class="taches-list">
        <?php if (!empty($result_taches)): ?>
            <?php foreach ($result_taches as $row): ?>
                <div class="tache">
                    <span id="tache-title"><?php echo htmlspecialchars($row['tache_title']); ?></span>
                    <div class="status">
                        <span>Status: </span>
                        <span id="tache-status">
                            <?php
                            if ($row['status'] == true) echo "Terminée";
                            if ($row['status'] == false) echo "En cours";
                            if ($row['status'] == null) echo "Annulée";
                            ?>
                        </span>
                    </div>

                    <div class="tache-board">
                        <span>Board: </span>
                        <span id="tache-board-name">
                            <?php echo isset($row['board_name']) ? $row['board_name'] : "Non spécifié"; ?>
                        </span>
                    </div>

                    <div class="tache-added-date">
                        <span>Ajoutée le: </span>
                        <span id="tache-date">
                            <?php echo !empty($row['created_at']) ? date("d-m-Y", strtotime($row['created_at'])) : "Non spécifié"; ?>
                        </span>
                    </div>

                    <div class="tache-finishing-date">
                        <span>Délai prévu: </span>
                        <span id="finishing-date">
                            <?php echo !empty($row['finishing_at']) ? date("d-m-Y", strtotime($row['finishing_at'])) : "Non spécifié"; ?>
                        </span>
                    </div>

                    <div class="options">
                        <div>
                            <form method="POST" action="update_status.php">
                                <input type="hidden" name="tache_id" value="<?php echo $row['tache_id']; ?>">
                                <input type="hidden" name="status" value="NULL">
                                <button type="submit" id="annuler">Annuler</button>
                            </form>
                            <a href="edittask.php?id=<?php echo $row['tache_id']; ?>">
                                <button id="modifier">Modifier</button>
                            </a>
                        </div>
                        <div>
                            <form method="POST" action="update_status.php">
                                <input type="hidden" name="tache_id" value="<?php echo $row['tache_id']; ?>">
                                <input type="hidden" name="status" value="1">
                                <button type="submit" id="termine">Terminer</button>
                            </form>
                            <a href="task.php?id=<?php echo $row['tache_id']; ?>">
                                <button id="voir">Voir</button>
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune tâche disponible pour cette board.</p>
        <?php endif; ?>
    </div>

</div>

<footer>
    2024 &copy; tous les droits réservés
</footer>

</body>

</html>
