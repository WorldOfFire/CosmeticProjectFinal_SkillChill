<?php
    session_start();
    require_once 'config.php';
    require_once 'configmail.php';

    $redirect = $_POST['redirect_to'] ?? 'users_add_panel.php?user=' . urlencode($_SESSION['user']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(header("Location: $redirect"));

    function resetPassword($pdo, $user_id) {
        $password_plain = bin2hex(random_bytes(4));
        $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE portal_user SET password_hash = ?, first_login = 0 WHERE id_user = ?");
        $stmt->execute([$password_hash, $user_id]);

        $stmtUser = $pdo->prepare("
            SELECT u.login, e.mail, e.name, e.surname
            FROM portal_user u
            JOIN company_employees e ON u.employees_id = e.id
            WHERE u.id_user = ?
        ");
        $stmtUser->execute([$user_id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$user) return;

        $folder = __DIR__ . '/userdata';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $filePath = $folder . '/resetdata.txt';
        file_put_contents($filePath, "Login: {$user['login']} | Nowe hasło: $password_plain\n", FILE_APPEND);

        // Wysyłanie maila (zakomentowane)
        /*
        try {
            $mail = getMailer();
            $mail->addAddress($user['mail'], $user['name'] . ' ' . $user['surname']);
            $mail->Subject = "Twoje nowe hasło";
            $mail->Body = "Witaj {$user['name']} {$user['surname']},\n\nTwoje hasło zostało zresetowane.\nLogin: {$user['login']}\nNowe hasło: $password_plain\n\nZaloguj się i zmień hasło po pierwszym logowaniu.";
            $mail->send();
        } catch (Exception $e) {
            // obsługa błędu wysyłki maila
        }
        */
    }

    if (isset($_POST['reset'])) {
        resetPassword($pdo, (int)$_POST['reset']);
        $_SESSION['success'] = "Hasło użytkownika zostało zresetowane.";
    }

    if (isset($_POST['reset-all']) && !empty($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $user_id) {
            resetPassword($pdo, (int)$user_id);
        }
        $_SESSION['success'] = "Hasła zaznaczonych użytkowników zostały zresetowane.";
    }

    header("Location: $redirect");
    exit;
?>