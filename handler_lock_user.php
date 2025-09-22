<?php
    session_start();
    require_once 'config.php';

    $redirect = $_POST['redirect_to'] ?? 'users_settings_panel.php?user=' . urlencode($_SESSION['user']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(header("Location: $redirect"));

    function lockUser($pdo, $user_id) {
        $stmt = $pdo->prepare("
            UPDATE user_attempts 
            SET login_attempts = 3, account_access = 1 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
    }

    if (isset($_POST['lock'])) {
        lockUser($pdo, (int)$_POST['lock']);
        $_SESSION['success'] = "Użytkownik został zablokowany.";
    }

    if (isset($_POST['lock-all']) && !empty($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $user_id) {
            lockUser($pdo, (int)$user_id);
        }
        $_SESSION['success'] = "Zaznaczeni użytkownicy zostali zablokowani.";
    }

    header("Location: $redirect");
    exit;
?>
