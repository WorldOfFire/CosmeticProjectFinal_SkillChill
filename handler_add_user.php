<?php
    session_start();
    require_once 'config.php';
    require_once 'configmail.php';

    $redirect = $_POST['redirect_to'] ?? 'users_add_panel.php?user=' . urlencode($_SESSION['user']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $redirect?error=" . urlencode("Nieprawidłowe żądanie."));
        exit;
    }

    function generateLogin($pdo, $name, $surname) {
        $baseLogin = strtolower(substr($name, 0, 1) . $surname);
        $baseLogin = iconv('UTF-8', 'ASCII//TRANSLIT', $baseLogin);
        $baseLogin = preg_replace('/[^a-z0-9]/', '', $baseLogin);

        $login = $baseLogin;
        $i = 1;
        while (true) {
            $stmtCheck = $pdo->prepare("SELECT id_user FROM portal_user WHERE login = ?");
            $stmtCheck->execute([$login]);
            if (!$stmtCheck->fetch()) break;
            $login = $baseLogin . $i;
            $i++;
        }
        return $login;
    }

    function createUser($pdo, $personel_id, $access, $person) {
        $login = generateLogin($pdo, $person['name'], $person['surname']);
        $password_plain = bin2hex(random_bytes(4));
        $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

        $stmtInsert = $pdo->prepare("INSERT INTO portal_user (employees_id, login, password_hash, access, first_login) VALUES (?, ?, ?, ?, 0)");
        $stmtInsert->execute([$personel_id, $login, $password_hash, $access]);
        $user_id = $pdo->lastInsertId();

        $stmtAttempts = $pdo->prepare("INSERT INTO user_attempts (user_id, login_attempts, account_access) VALUES (?, 0, 0)");
        $stmtAttempts->execute([$user_id]);

        $stmtUpdate = $pdo->prepare("UPDATE company_employees SET registered = 1 WHERE id = ?");
        $stmtUpdate->execute([$personel_id]);

        $userdataFolder = __DIR__ . '/userdata';
        if (!is_dir($userdataFolder)) mkdir($userdataFolder, 0755, true);

        $filePath = $userdataFolder . '/userdata.txt';
        file_put_contents($filePath, "Login: $login | Hasło: $password_plain | Access: $access\n", FILE_APPEND);

        // ------------------ WYSYŁANIE MAILA (zakomentowane) ------------------
        /*
        try {
            $mail = getMailer();
            $mail->addAddress($person['mail'], $person['name'] . ' ' . $person['surname']);
            $mail->Subject = "Twoje konto zostało aktywowane";
            $mail->Body = "Witaj {$person['name']} {$person['surname']},\n\n"
                        . "Twoje konto zostało utworzone.\n"
                        . "Login: $login\n"
                        . "Hasło: $password_plain\n"
                        . "Poziom dostępu: $access\n\n"
                        . "Zaloguj się i zmień hasło po pierwszym logowaniu.";
            $mail->send();
        } catch (Exception $e) {
            $_SESSION['success'] = "Użytkownik {$person['name']} {$person['surname']} został zatwierdzony, ale nie udało się wysłać maila: {$mail->ErrorInfo}";
        }
        */
        // ----------------------------------------------------------------------

        return "{$person['name']} {$person['surname']}";
    }

    if (isset($_POST['confirm'])) {
        $personel_id = (int)$_POST['confirm'];
        $access = $_POST['access_level'][$personel_id] ?? 0;

        $stmt = $pdo->prepare("SELECT name, surname, mail FROM company_employees WHERE id = ? AND registered = 0");
        $stmt->execute([$personel_id]);
        $person = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($person) {
            $username = createUser($pdo, $personel_id, $access, $person);
            $_SESSION['success'] = "Użytkownik $username został zatwierdzony z access = $access.";
        }
        header("Location: $redirect");
        exit;
    }

    if (isset($_POST['confirm-all']) && !empty($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $personel_id) {
            $personel_id = (int)$personel_id;
            $access = $_POST['access_level'][$personel_id] ?? 0;

            $stmt = $pdo->prepare("SELECT name, surname, mail FROM company_employees WHERE id = ? AND registered = 0");
            $stmt->execute([$personel_id]);
            $person = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($person) {
                createUser($pdo, $personel_id, $access, $person);
            }
        }
        $_SESSION['success'] = "Zatwierdzono wszystkich wybranych użytkowników.";
        header("Location: $redirect");
        exit;
    }

    $_SESSION['success'] = "Nie zaznaczono żadnego użytkownika do zatwierdzenia.";
    header("Location: $redirect");
    exit;
?>
