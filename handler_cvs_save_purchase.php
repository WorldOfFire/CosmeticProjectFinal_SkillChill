<?php
    session_start();
    header('Content-Type: application/json');

    if (empty($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Niepoprawne dane JSON']);
        exit;
    }

    $dateFolder = __DIR__ . '/DATE/PURCHASE_REPORT';
    if (!is_dir($dateFolder)) {
        mkdir($dateFolder, 0777, true);
    }

    $quarter = $data['quarter'] ?? '0';
    $year = $data['year'] ?? date('Y');
    $userName = $_SESSION['user_name'] ?? 'unknown';
    $userSurname = $_SESSION['user_surname'] ?? 'unknown';
    $userIndex = $_SESSION['user_index'] ?? '0';

    $filename = "purchase_report_Q{$quarter}_{$year}_{$userName}_{$userSurname}_{$userIndex}.csv";
    $filepath = $dateFolder . '/' . $filename;

    $fp = fopen($filepath, 'w');
    if (!$fp) {
        echo json_encode(['success' => false, 'message' => 'Nie można utworzyć pliku CSV']);
        exit;
    }

    fwrite($fp, "\xEF\xBB\xBF");

    fputcsv($fp, [
        'Purchase_Report_Last_Year_Sales',
        'Purchase_Report_Purchases',
        'Purchase_Report_Budget',
        'Purchase_Report_Actual_Sales',
        'Purchase_Report_Total_vs_Last_Year',
        'Purchase_Report_Total_vs_Budget',
        'Purchase_Report_Total_POS',
        'Purchase_Report_New_Openings',
        'Purchase_Report_New_Openings_Target'
    ]);

    $total_vs_last_year = ($data['last_year_sales_pl'] ?? 0) - ($data['last_year_sales_pl'] ?? 0);
    $total_vs_budget = ($data['actual_sales_pl'] ?? 0) - ($data['budget_pl'] ?? 0);

    $row = [
        $data['last_year_sales_pl'] ?? '',
        $data['purchases_pl'] ?? '',
        $data['budget_pl'] ?? '',
        $data['actual_sales_pl'] ?? '',
        $total_vs_last_year,
        $total_vs_budget,
        $data['total_pos'] ?? '',
        $data['new_openings'] ?? '',
        $data['new_openings_target'] ?? ''
    ];

    fputcsv($fp, $row);
    fclose($fp);

    echo json_encode([
        'success' => true,
        'file_path' => str_replace(__DIR__ . '/', '', $filepath),
        'file_name' => $filename
    ]);
?>
