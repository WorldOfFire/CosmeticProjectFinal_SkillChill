<?php
    session_start();
    require_once 'config.php';

    $redirect = $_POST['redirect_to'] ?? 'users_add_panel.php?user=' . urlencode($_SESSION['user']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $redirect?error=" . urlencode("Nieprawidłowe żądanie."));
        exit;
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete'];

        $stmtUser = $pdo->prepare("SELECT id_user FROM portal_user WHERE employees_id = ?");
        $stmtUser->execute([$id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $pdo->prepare("DELETE FROM user_attempts WHERE user_id = ?")->execute([$user['id_user']]);
            $pdo->prepare("DELETE FROM portal_user WHERE id_user = ?")->execute([$user['id_user']]);
        }

        $pdo->prepare("DELETE FROM company_employees WHERE id = ?")->execute([$id]);

        $_SESSION['success'] = "Użytkownik został usunięty.";
        header("Location: $redirect");
        exit;
    }

    if (isset($_POST['delete-all']) && !empty($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $id) {
            $id = (int)$id;

            $stmtUser = $pdo->prepare("SELECT id_user FROM portal_user WHERE employees_id = ?");
            $stmtUser->execute([$id]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $pdo->prepare("DELETE FROM user_attempts WHERE user_id = ?")->execute([$user['id_user']]);
                $pdo->prepare("DELETE FROM portal_user WHERE id_user = ?")->execute([$user['id_user']]);
            }

            $pdo->prepare("DELETE FROM company_employees WHERE id = ?")->execute([$id]);
        }
        $_SESSION['success'] = "Usunięto wszystkich zaznaczonych użytkowników.";
        header("Location: $redirect");
        exit;
    }

    header("Location: $redirect");
    exit;
?>
