<?php
    require 'config.php';
    session_start();

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $redirect = $_POST['redirect_to'] ?? 'main_panel.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $repeatPassword = $_POST['new_repeat_password'] ?? '';
        $username = $_SESSION['user'];

        if (!$oldPassword || !$newPassword || !$repeatPassword) {
            $_SESSION['error'] = "Uzupełnij wszystkie pola.";
            header("Location: $redirect");
            exit;
        }

        if ($newPassword !== $repeatPassword) {
            $_SESSION['error'] = "Hasła się nie zgadzają.";
            header("Location: $redirect");
            exit;
        }

        if (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
            $_SESSION['error'] = "Nowe hasło musi mieć co najmniej 8 znaków, 1 znak specjalny oraz przynajmniej 1 liczbę.";
            header("Location: $redirect");
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT password_hash FROM portal_user WHERE login = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
                $_SESSION['error'] = "Stare hasło jest niepoprawne.";
                header("Location: $redirect");
                exit;
            }

            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE portal_user SET password_hash = ?, first_login = 1 WHERE login = ?");
            $update->execute([$newHash, $username]);

            $_SESSION['firstLogin'] = 1;
            $_SESSION['success'] = "Hasło zostało zmienione.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Wystąpił błąd. Spróbuj ponownie później.";
        }
    }

    header("Location: $redirect");
    exit;
?>