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
            // Récupérer les détails de la tâche
            $stmt_task = $pdo->prepare("SELECT * FROM taches WHERE tache_id = ? AND email = ?");
            $stmt_task->execute([$task_id, $email]);
            $task = $stmt_task->fetch(PDO::FETCH_ASSOC);
        }

        // Traiter le formulaire de mise à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tache_title = $_POST['tache_title'];
            $status = isset($_POST['status']) ? $_POST['status'] : null;
            $finishing_at = !empty($_POST['finishing_at']) ? $_POST['finishing_at'] : null;

            $stmt_update = $pdo->prepare("UPDATE taches SET tache_title = ?, status = ?, finishing_at = ? WHERE tache_id = ?");
            $stmt_update->execute([$tache_title, $status, $finishing_at, $task_id]);

            $_SESSION['message'] = "La tâche a été mise à jour avec succès.";
            header("Location: dashboard.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier une tâche - Workify</title>
    <link rel="stylesheet" href="css/edittask.css">
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
        <h2>Modifier la tâche</h2>
        <form method="POST" action="edittask.php?id=<?php echo $task_id; ?>">
            <div class="form-group">
                <label for="tache_title">Titre de la tâche :</label>
                <input type="text" id="tache_title" name="tache_title" value="<?php echo htmlspecialchars($task['tache_title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Statut :</label>
                <select id="status" name="status">
                    <option value="1" <?php echo $task['status'] == "1" ? "selected" : ""; ?>>Terminée</option>
                    <option value="0" <?php echo $task['status'] == "0" ? "selected" : ""; ?>>En cours</option>
                    <option value="NULL" <?php echo $task['status'] === null ? "selected" : ""; ?>>Annulée</option>
                </select>
            </div>
            <div class="form-group">
                <label for="finishing_at">Date d'échéance :</label>
                <input type="date" id="finishing_at" name="finishing_at" value="<?php echo htmlspecialchars($task['finishing_at']); ?>">
            </div>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </form>
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
