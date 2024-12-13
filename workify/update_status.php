<?php
session_start();

try {
    $host = "localhost";
    $dbname = "workify";
    $username = "root";
    $password = "";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['tache_id'])) {
        $tache_id = $_POST['tache_id'];
        $status = $_POST['status'] === "NULL" ? null : $_POST['status'];

        $stmt = $pdo->prepare("UPDATE taches SET status = :status WHERE tache_id = :tache_id");
        $stmt->bindParam(':status', $status, is_null($status) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':tache_id', $tache_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Aucun identifiant de tâche fourni.";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
