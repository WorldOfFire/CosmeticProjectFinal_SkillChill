<?php
session_start();

if (empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nie jesteś zalogowany']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require 'config.php';

$quarter = isset($_GET['quarter']) ? intval($_GET['quarter']) : null;
$year = isset($_GET['year']) ? intval($_GET['year']) : null;

if (!$quarter || !$year) {
    echo json_encode(['error' => 'Brak kwartału lub roku']);
    exit;
}

try {
    // Pobranie wszystkich raportów dla bieżącego kwartału i roku
    $stmt = $pdo->prepare("SELECT id_report FROM report WHERE quarter = ? AND year = ?");
    $stmt->execute([$quarter, $year]);
    $currentReports = $stmt->fetchAll();

    $totalCurrentPln = 0;
    $totalCurrentEur = 0;
    $channelsSum = [];

    foreach ($currentReports as $report) {
        $stmt = $pdo->prepare("
            SELECT sc.sale_pln, sc.sale_eur, sc.sales_channel_id, scn.sale_channel_name
            FROM sales_channels sc
            JOIN sales_channels_name scn ON sc.sales_channel_id = scn.id_sales_channel
            WHERE sc.report_id = ?
        ");
        $stmt->execute([$report['id_report']]);
        $channels = $stmt->fetchAll();

        foreach ($channels as $ch) {
            $totalCurrentPln += floatval($ch['sale_pln']);
            $totalCurrentEur += floatval($ch['sale_eur']);

            if (!isset($channelsSum[$ch['sales_channel_id']])) {
                $channelsSum[$ch['sales_channel_id']] = [
                    'id' => $ch['sales_channel_id'],
                    'name' => $ch['sale_channel_name'],
                    'sale_pln' => 0,
                    'sale_eur' => 0
                ];
            }
            $channelsSum[$ch['sales_channel_id']]['sale_pln'] += floatval($ch['sale_pln']);
            $channelsSum[$ch['sales_channel_id']]['sale_eur'] += floatval($ch['sale_eur']);
        }
    }

    // Zaokrąglanie sum bieżącego kwartału
    $totalCurrentPln = round($totalCurrentPln, 2);
    $totalCurrentEur = round($totalCurrentEur, 2);
    foreach ($channelsSum as &$ch) {
        $ch['sale_pln'] = round($ch['sale_pln'], 2);
        $ch['sale_eur'] = round($ch['sale_eur'], 2);
    }

    // Pobranie wszystkich raportów dla tego samego kwartału rok wcześniej
    $stmt = $pdo->prepare("SELECT id_report FROM report WHERE quarter = ? AND year = ?");
    $stmt->execute([$quarter, $year - 1]);
    $lastYearReports = $stmt->fetchAll();

    $totalLastYearPln = 0;
    $totalLastYearEur = 0;

    foreach ($lastYearReports as $report) {
        $stmt = $pdo->prepare("
            SELECT SUM(sale_pln) as sum_pln, SUM(sale_eur) as sum_eur
            FROM sales_channels
            WHERE report_id = ?
        ");
        $stmt->execute([$report['id_report']]);
        $sums = $stmt->fetch();
        $totalLastYearPln += floatval($sums['sum_pln']);
        $totalLastYearEur += floatval($sums['sum_eur']);
    }

    // Zaokrąglanie sum poprzedniego roku
    $totalLastYearPln = round($totalLastYearPln, 2);
    $totalLastYearEur = round($totalLastYearEur, 2);

    $response = [
        'totalCurrentPln' => $totalCurrentPln,
        'totalCurrentEur' => $totalCurrentEur,
        'totalLastYearPln' => $totalLastYearPln,
        'totalLastYearEur' => $totalLastYearEur,
        'channels' => array_values($channelsSum)
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
