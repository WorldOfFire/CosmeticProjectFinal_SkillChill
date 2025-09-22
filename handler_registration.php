<?php
require 'config.php';

$redirect = 'sign_out.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $mail    = trim($_POST['mail'] ?? '');
    $business_country = trim($_POST['country'] ?? '');
    $dis_index = trim($_POST['dis_index'] ?? '');

    if (!$name || !$surname || !$mail || !$business_country || !$dis_index) {
        header("Location: $redirect?error=" . urlencode("Wszystkie pola muszą być wypełnione."));
        exit;
    }

    try {
        require 'country_map.php';

        $countryCode = array_search($business_country, $countryMap, true);
        if ($countryCode === false) {
            header("Location: $redirect?error=" . urlencode("Wybrany kraj nie jest obsługiwany. Skontaktuj się z administratorem."));
            exit;
        }

        $datePart = date('y') . date('m');

        // Generowanie unikalnego indeksu
        do {
            $randomNumber = rand(1000, 9999);
            $index = $countryCode . $datePart . $randomNumber;

            $check = $pdo->prepare("SELECT COUNT(*) FROM company_employees WHERE employees_index = ?");
            $check->execute([$index]);
            $exists = $check->fetchColumn() > 0;
        } while ($exists);

        // Dodanie pracownika
        $stmt = $pdo->prepare("
            INSERT INTO company_employees (name, surname, mail, employees_index, registered, country)
            VALUES (?, ?, ?, ?, 0, ?)
        ");
        $stmt->execute([$name, $surname, $mail, $index, $business_country]);

        // Pobranie ID nowego pracownika
        $stmt2 = $pdo->prepare("SELECT id FROM company_employees WHERE employees_index = ?");
        $stmt2->execute([$index]);
        $employee = $stmt2->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            // Jeśli nie udało się pobrać pracownika, usuwamy rekord
            $pdo->prepare("DELETE FROM company_employees WHERE employees_index = ?")->execute([$index]);
            header("Location: $redirect?error=" . urlencode("Wystąpił błąd: nie udało się znaleźć pracownika."));
            exit;
        }

        // Pobranie ID dystrybutora
        $stmt3 = $pdo->prepare("SELECT id FROM distributors WHERE distributor_index = ?");
        $stmt3->execute([$dis_index]);
        $distributor = $stmt3->fetch(PDO::FETCH_ASSOC);

        if (!$distributor) {
            // Jeśli dystrybutor nie istnieje, usuwamy nowego pracownika
            $pdo->prepare("DELETE FROM company_employees WHERE employees_index = ?")->execute([$index]);
            header("Location: $redirect?error=" . urlencode("Dystrybutor o podanym indeksie nie istnieje."));
            exit;
        }

        // Dodanie powiązania pracownik-dystrybutor
        $stmt4 = $pdo->prepare("
            INSERT INTO distributor_employees (distributor_id, employee_id)
            VALUES (?, ?)
        ");
        $stmt4->execute([$distributor['id'], $employee['id']]);

        header("Location: $redirect?success=" . urlencode("Rejestracja zakończona pomyślnie. Administrator musi zatwierdzić Twoje konto przed pierwszym logowaniem."));
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $check = $pdo->prepare("SELECT registered FROM company_employees WHERE mail = ? OR employees_index = ?");
            $check->execute([$mail, $index]);
            $user = $check->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $msg = ((int)$user['registered'] === 1)
                    ? "Pracownik o podanym e-mailu lub indeksie jest już zarejestrowany."
                    : "Pracownik o podanym e-mailu lub indeksie już się zarejestrował i oczekuje na zatwierdzenie administratora.";
            } else {
                $msg = "Podany e-mail lub indeks pracownika już istnieje.";
            }

            header("Location: $redirect?error=" . urlencode($msg));
        } else {
            header("Location: $redirect?error=" . urlencode("Wystąpił błąd podczas rejestracji. Spróbuj ponownie później."));
        }
        exit;
    }
}

header("Location: $redirect?error=" . urlencode("Nieprawidłowe żądanie."));
exit;
