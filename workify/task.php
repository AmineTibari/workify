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

        // Vérifier si l'ID de la tâche est fourni
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $task = null;

        if ($task_id) {
            // Requête pour récupérer les détails de la tâche
            $stmt_task = $pdo->prepare("SELECT * FROM taches WHERE tache_id = ?");
            $stmt_task->execute([$task_id]);
            $task = $stmt_task->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Afficher une tâche - Workify</title>
    <link rel="stylesheet" href="css/task.css">
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
    <?php if ($task): ?>
        <h2>Détails de la tâche</h2>
        <div class="task-details">
            <p><strong>Titre de la tâche :</strong> <?php echo htmlspecialchars($task['tache_title']); ?></p>
            <p><strong>Statut :</strong> <?php
                echo $task['status'] === "1" ? "Terminée" : ($task['status'] === "0" ? "En cours" : "Annulée");
                ?></p>
            <p><strong>Board :</strong> <?php
                if ($task['board_name'] == NULL) {
                    echo "Non spécifié";
                } else {
                    echo $task['board_name'];
                }
                ?></p>
            <p><strong>Date de création :</strong> <?php echo htmlspecialchars($task['created_at']); ?></p>
            <p><strong>Date d'échéance :</strong> <?php echo htmlspecialchars(isset($task['finishing_at']) ? $task['finishing_at'] : "Non définie"); ?></p>
        </div>
    <?php else: ?>
        <h2>Tâche introuvable</h2>
        <p>Aucune tâche trouvée avec cet identifiant.</p>
    <?php endif; ?>
</div>

<footer>
    2024 &copy; Tous droits réservés
</footer>

</body>

</html>
