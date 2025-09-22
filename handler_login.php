<?php
    require_once('config.php');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['login']) || empty($_POST['password'])) {
        header("Location: sign_in.php?error=" . urlencode("Uzupełnij oba pola"));
        exit;
    }

    $login = $_POST['login'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT pu.*, ce.* FROM portal_user pu JOIN company_employees ce ON pu.employees_id = ce.id WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: sign_in.php?error=" . urlencode("Nieprawidłowy login lub hasło"));
            exit;
        }

        $stmt2 = $pdo->prepare("SELECT * FROM user_attempts WHERE user_id = ?");
        $stmt2->execute([$user['id_user']]);
        $attempt = $stmt2->fetch(PDO::FETCH_ASSOC);

        if (!$attempt) {
            $stmt_insert = $pdo->prepare("INSERT INTO user_attempts (user_id, login_attempts, account_access) VALUES (?, 0, 0)");
            $stmt_insert->execute([$user['id_user']]);
            $attempt = ['login_attempts' => 0, 'account_access' => 0];
        }

        if ($attempt['account_access'] == 1) {
            header("Location: sign_in.php?error=" . urlencode("Konto zostało zablokowane. Skontaktuj się z administratorem."));
            exit;
        }

        if (password_verify($password, $user['password_hash'])) {

            $stmt3 = $pdo->prepare("SELECT distributor_id FROM distributor_employees WHERE employee_id = ?");
            $stmt3->execute([$user['employees_id']]);
            $user2 = $stmt3->fetch(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION['user'] = htmlspecialchars($user['login']);
            $_SESSION['user_name'] = htmlspecialchars($user['name']);
            $_SESSION['user_surname'] = htmlspecialchars($user['surname']);
            $_SESSION['user_index'] = htmlspecialchars($user['employees_index']);
            $_SESSION['user_country'] = htmlspecialchars($user['country']);
            $_SESSION['user_mail'] = htmlspecialchars($user['mail']);
            $_SESSION['dis_index'] = htmlspecialchars($user2['distributor_id']);
            $_SESSION['id_user'] = (int)$user['id_user'];
            $_SESSION['firstLogin'] = (int)$user['first_login'];
            $_SESSION['access'] = (int)$user['access'];
            $_SESSION['employees_id'] = (int)$user['employees_id'];

            $stmt_reset = $pdo->prepare("UPDATE user_attempts SET login_attempts = 0 WHERE user_id = ?");
            $stmt_reset->execute([$user['id_user']]);

            header("Location: main_panel.php?user=" . urlencode($user['login']));
            exit;
        } else {

            $new_attempts = $attempt['login_attempts'] + 1;
            $account_access = ($new_attempts >= 3) ? 1 : 0;

            $stmt_update = $pdo->prepare("UPDATE user_attempts SET login_attempts = ?, account_access = ? WHERE user_id = ?");
            $stmt_update->execute([$new_attempts, $account_access, $user['id_user']]);

            $error_msg = ($account_access == 1)
                ? "Konto zostało zablokowane. Skontaktuj się z administratorem."
                : "Nieprawidłowy login lub hasło. Próba {$new_attempts} z 3.";

            header("Location: sign_in.php?error=" . urlencode($error_msg));
            exit;
        }

    } catch (PDOException $e) {
        header("Location: sign_in.php?error=" . urlencode("Wystąpił błąd serwera, spróbuj ponownie."));
        exit;
    }
