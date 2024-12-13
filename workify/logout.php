<?php

session_start();

if (isset($_SESSION['email'])) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
} else {
    echo "No active session found.";
}

