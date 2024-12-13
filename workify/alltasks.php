<?php
session_start();

$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;

// تحقق من تسجيل الدخول
if (!$email) {
    die("Veuillez vous connecter pour accéder à vos tâches.");
}

try {
    $host = "localhost";
    $dbname = "workify";
    $username = "root";
    $password = "";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب جميع المهام الخاصة بالمستخدم
    $stmt_taches = $pdo->prepare("SELECT * FROM taches WHERE email = ? ORDER BY tache_id DESC;");
    $stmt_taches->execute([$email]);
    $result_taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Toutes les tâches - Workify</title>
    <link rel="stylesheet" href="css/alltasks.css">
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

    <div class="tasks-header">
        <h2>Liste de toutes les tâches</h2>
        <a href="addtask.php">
            <button>Ajouter une tâche</button>
        </a>
    </div>

    <div class="tasks-list">
        <?php if (!empty($result_taches)): ?>
            <?php foreach ($result_taches as $row): ?>
                <div class="task">
                    <h3><?php echo htmlspecialchars($row['tache_title']); ?></h3>
                    <p><strong>Board :</strong> <?php
                        if ($row['board_name'] == NULL) {
                            echo "Non spécifié";
                        } else {
                            echo $row['board_name'];
                        }
                        ?></p>
                    <p><strong>Status:</strong>
                        <?php
                        if ($row['status'] == true) echo "Terminée";
                        if ($row['status'] == false) echo "En cours";
                        if ($row['status'] === null) echo "Annulée";
                        ?>
                    </p>
                    <p><strong>Ajoutée le:</strong> <?php echo date("d-m-Y", strtotime($row['created_at'])); ?></p>
                    <p><strong>Délai prévu:</strong> <!--?php echo date("d-m-Y", strtotime($row['finishing_at'])); ?-->
                        <?php if ($row['finishing_at'] == NULL) {
                        echo "Non spécifié";
                        } else {
                        echo $row['finishing_at'];
                        }?>
                    </p>
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
            <p>Aucune tâche trouvée.</p>
        <?php endif; ?>
    </div>

</div>

<footer>
    <p>&copy; 2024 Tous les droits réservés - Workify</p>
</footer>

</body>
</html>
