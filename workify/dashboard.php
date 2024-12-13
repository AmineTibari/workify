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

        // استعلام للحصول على المهام
        $stmt_taches = $pdo->prepare("SELECT * FROM taches WHERE status IS NOT NULL AND email = ? ORDER BY tache_id DESC;");
        $stmt_taches->execute([$email]);
        $result_taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);

        // استعلام للحصول على اللوحات مع عدد المهام المرتبطة بكل لوحة
        $stmt_boards = $pdo->prepare("
        SELECT boards.board_name, 
        COUNT(CASE WHEN taches.status = 0 THEN 1 END) AS task_in_progress,
        COUNT(CASE WHEN taches.status = 1 THEN 1 END) AS task_completed
        FROM boards
        LEFT JOIN taches ON boards.board_name = taches.board_name
        WHERE boards.email = ?
        GROUP BY boards.board_name;
    ");
        $stmt_boards->execute([$email]);
        $result_boards = $stmt_boards->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>connection - workify</title>
    <link rel="stylesheet" href="css/dashboard.css">
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


    <div class="boards">

        <div class="boards-title">
            <span>Liste des boards</span>
            <div>

                <a href="addboard.php">
                    <button>Ajouter une board</button>
                </a>

                <a href="allboards.php">
                    <button>voir tous les boards</button>
                </a>
            </div>

        </div>
        <div class="boards-list">
            <?php if (!empty($result_boards)): ?>
                <?php
                $limited_boards = array_slice($result_boards, 0, 8);
                ?>
                <?php foreach ($limited_boards as $row): ?>
                    <div class="board">
                        <span id="board-title"><?php echo htmlspecialchars($row['board_name']); ?></span>

                        <div class="in_progress">
                            <span>Tâches en cours: </span>
                            <span><?php echo $row['task_in_progress']; ?></span>
                        </div>
                        <div class="completed">
                            <span>Tâches terminee: </span>
                            <span><?php echo $row['task_completed']; ?></span>
                        </div>
                        <div class="see_board_div">

                            <form method="GET" action="board.php">
                                <input type="hidden" name="board_name" value="<?php echo htmlspecialchars($row['board_name']); ?>">
                                <button type="submit" id="see_board">Voir</button>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <div class="taches">

        <div class="taches-title">
            <span>Liste des taches</span>
            <div>
                <a href="addtask.php">
                    <button>Ajouter une tache</button>
                </a>
                <a href="alltasks.php">
                    <button>voir tous les taches</button>
                </a>
            </div>
        </div>
        <div class="taches-list">
            <?php if (!empty($result_taches)): ?>
                <?php
                $limited_taches = array_slice($result_taches, 0, 8);
                ?>
                <?php foreach ($limited_taches as $row): ?>
                    <div class="tache">
                        <span id="tache-title"><?php echo htmlspecialchars($row['tache_title']); ?></span>
                        <div class="status">
                            <span>Status: </span>
                            <span id="tache-status">
                                <?php if ($row['status'] == true) echo "Terminée" ?>
                                <?php if ($row['status'] == false) echo "En cours" ?>
                                <?php if ($row['status'] == null) echo "Annulée" ?>
                            </span>
                        </div>

                        <div class="tache-board">

                            <span>Board: </span>
                            <span id="tache-board-name">
                                <?php if ($row['board_name'] == NULL) {
                                    echo "Non spécifié";
                                } else {
                                    echo $row['board_name'];
                                } ?>
                            </span>

                        </div>

                        <div class="tache-added-date">
                            <span>Ajoutée le: </span>
                            <span id="tache-date">
                            <?php
                            if (!empty($row['created_at'])) {
                                echo date("d-m-Y", strtotime($row['created_at']));
                            } else {
                                echo "Non spécifié";
                            }
                            ?>
                            </span>
                        </div>

                        <div class="tache-finishing-date">

                            <span>Délai prévu: </span>

                            <span id="finishing-date">
                                <?php
                                if (!empty($row['finishing_at'])) {
                                    echo date("d-m-Y", strtotime($row['created_at']));
                                } else {
                                    echo "Non spécifié";
                                }
                                ?>
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
            <?php endif; ?>
        </div>

    </div>

</div>

<footer>
    2024 &copy; tous les droits réservés
</footer>

</body>

</html>
