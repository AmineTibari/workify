<?php

session_start();

$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;

if ($email == null) {
header("Location: login.html");
exit;
} else {
    header("Location: dashboard.php");
    exit;

}