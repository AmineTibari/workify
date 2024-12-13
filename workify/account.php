

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
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
}

    ?>
<!DOCTYPE html>
<html>

    <head>

        <meta charset="UTF-8">
        <title>compte - workify</title>
        <link rel="stylesheet" href="css/account.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    </head>

    <body>

        <header>
            <a href="dashboard.php"><span id="logo">workify</span></a>
            <span id="email-text">Bonjour,
                <?php echo $email ?>
            </span>
        </header>

        <div class="container">

            <div class="account-section">
                <span id="titre">compte</span>

                <form id="username-form" method="POST" action="editaccount.php">
                    <input type="text" name="type" value="username" hidden="hidden">
                    <span id="username-form-label">Nom: </span><br>
                    <input type="text" value="<?php echo $user['username'] ?>" name="username">
                    <input type="submit" value="change">
                </form>
                <div class="email-zone">
                    <label id="email-label">Email:</label>
                    <p id="email-user"><?php echo $user['email'] ?></p><br>
                </div>

                <label id="password-form-label">Changer le mot de passe:</label>
                <form id="password-form" action="editaccount.php" method="POST">
                    <input type="text" name="type" value="password" hidden="hidden">
                    <input type="password" name="actual-password" placeholder="mot de passe actuel">
                    <input type="password" name="new-password" placeholder="nouvelle mot de passe">
                    <input type="submit" value="changer">
                </form>

                <form id="logout-form" method="POST" action="logout.php">
                    <label id="logout-label">deconnexion</label>
                    <input type="submit" value="Log out">
                </form>
            </div>

        </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const usernameDisplay = document.querySelector(".display-username");
            const editSection = document.querySelector(".edit-username");

            console.log(editSection.style.display);
            usernameDisplay.addEventListener("click", () => {
                if (editSection.style.display === "none" || editSection.style.display === "") {
                    editSection.style.display = "block";
                } else {
                    editSection.style.display = "none";
                }
            });


            const button = document.getElementById("button-changement-password");
            const form = document.querySelector(".password form");

            button.addEventListener("click", () => {
                if (form.style.display === "none" || form.style.display === "") {
                    form.style.display = "block";
                } else {
                    form.style.display = "none";
                }
            });

        });
    </script>

    </body>

</html>
