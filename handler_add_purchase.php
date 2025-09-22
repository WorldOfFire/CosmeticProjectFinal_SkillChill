<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Niepoprawna metoda']);
    exit;
}

// Pobranie danych JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Brak danych']);
    exit;
}

// Wczytanie konfiguracji PDO
require_once 'config.php';

try {
    $stmt = $pdo->prepare("
        INSERT INTO purchase_report
        (name_creator, surname_creator, login_creator, index_creator,
         quarter, year,
         last_year_sales_pl, last_year_sales_eur,
         purchases_pl, purchases_eur,
         budget_pl, budget_eur,
         actual_sales_pl, actual_sales_eur,
         total_pos, new_openings, new_openings_target,
         created_at)
        VALUES
        (:name_creator, :surname_creator, :login_creator, :index_creator,
         :quarter, :year,
         :last_year_sales_pl, :last_year_sales_eur,
         :purchases_pl, :purchases_eur,
         :budget_pl, :budget_eur,
         :actual_sales_pl, :actual_sales_eur,
         :total_pos, :new_openings, :new_openings_target,
         NOW())
    ");

    $stmt->execute([
        ':name_creator' => $_SESSION['user_name'],
        ':surname_creator' => $_SESSION['user_surname'],
        ':login_creator' => $_SESSION['user'],
        ':index_creator' => $_SESSION['user_index'],
        ':quarter' => $data['quarter'],
        ':year' => $data['year'],
        ':last_year_sales_pl' => $data['last_year_sales_pl'],
        ':last_year_sales_eur' => $data['last_year_sales_eur'],
        ':purchases_pl' => $data['purchases_pl'],
        ':purchases_eur' => $data['purchases_eur'],
        ':budget_pl' => $data['budget_pl'],
        ':budget_eur' => $data['budget_eur'],
        ':actual_sales_pl' => $data['actual_sales_pl'],
        ':actual_sales_eur' => $data['actual_sales_eur'],
        ':total_pos' => $data['total_pos'],
        ':new_openings' => $data['new_openings'],
        ':new_openings_target' => $data['new_openings_target']
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
