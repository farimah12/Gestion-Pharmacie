<?php
//session_start();
require_once "db.php";


function isLogged() {
    return isset($_SESSION['user']);
}


function isAdmin() {
    return isLogged() && $_SESSION['user']['role'] === 'admin';
}


function isCaissier() {
    return isLogged() && $_SESSION['user']['role'] === 'caissier';
}


function requireLogin() {
    if (!isLogged()) {
        header("Location: index.php");
        exit;
    }
}


function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php?error=acces_refuse");
        exit;
    }
}


function requireCaissier() {
    requireLogin();
    if (!isCaissier()) {
        header("Location: dashboard.php?error=acces_refuse");
        exit;
    }
}