<?php
session_start();
require 'config.php';

if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUser = $_SESSION['id_user'];
    $idEmployee = $_SESSION['employees_id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $index = trim($_POST['index'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $login = trim($_POST['login'] ?? '');

    if (!$name || !$surname || !$mail || !$index || !$country || !$login) {
        $_SESSION['error'] = "Uzupełnij wszystkie pola.";
        header("Location: settings_panel.php?user=" . urlencode($_SESSION['user']));
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE company_employees 
                               SET name = ?, surname = ?, mail = ?, employees_index = ?, country = ?
                               WHERE id = ?");
        $stmt->execute([$name, $surname, $mail, $index, $country, $idEmployee]);

        $stmtUser = $pdo->prepare("UPDATE portal_user 
                                   SET login = ? 
                                   WHERE employees_id = ?");
        $stmtUser->execute([$login, $idEmployee]);

        $stmt2 = $pdo->prepare("
            SELECT ce.*, pu.login, pu.first_login, pu.access 
            FROM company_employees ce
            JOIN portal_user pu ON ce.id = pu.employees_id
            WHERE ce.id = ?
        ");
        $stmt2->execute([$idEmployee]);
        $user = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user'] = htmlspecialchars($user['login']);
            $_SESSION['user_name'] = htmlspecialchars($user['name']);
            $_SESSION['user_surname'] = htmlspecialchars($user['surname']);
            $_SESSION['user_index'] = htmlspecialchars($user['employees_index']);
            $_SESSION['user_country'] = htmlspecialchars($user['country']);
            $_SESSION['user_mail'] = htmlspecialchars($user['mail']);
            $_SESSION['id_user'] = (int)$user['id_user'];
            $_SESSION['employees_id'] = (int)$user['id'];
            $_SESSION['firstLogin'] = (int)$user['first_login'];
            $_SESSION['access'] = (int)$user['access'];

            $_SESSION['success'] = "Dane zostały zaktualizowane.";
        } else {
            $_SESSION['error'] = "Nie znaleziono użytkownika.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Wystąpił błąd podczas aktualizacji danych.";
    }

    header("Location: settings_panel.php?user=" . urlencode($_SESSION['user']));
    exit;
}
