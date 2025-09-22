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

    $dateFolder = __DIR__ . '/DATE/SALES_CHANNELS';
    if (!is_dir($dateFolder)) mkdir($dateFolder, 0777, true);

    $year = $data['year'] ?? date('Y');
    $quarter = $data['quarter'] ?? 'Q';

    $userName = $_SESSION['user_name'] ?? 'unknown';
    $userSurname = $_SESSION['user_surname'] ?? 'unknown';
    $userLogin = $_SESSION['user'] ?? 'unknown';
    $userIndex = $_SESSION['user_index'] ?? '000';

    $filename = "sales_report_Q{$quarter}_{$year}_{$userName}_{$userSurname}_{$userIndex}.csv";
    $filepath = $dateFolder . '/' . $filename;

    $fp = fopen($filepath, 'w');
    if (!$fp) {
        echo json_encode(['success' => false, 'message' => 'Nie można utworzyć pliku CSV']);
        exit;
    }

    fwrite($fp, "\xEF\xBB\xBF"); // BOM dla UTF-8

    // Nagłówki CSV
    fputcsv($fp, [
        'Distributor',
        'Currency',
        'Professional sales',
        'Pharmacy sales',
        'E-commerce sales B2C',
        'E-commerce sales B2B',
        'Third party',
        'Other',
        'Total sales',
        'New clients',
        'Professional sales in EUR',
        'Pharmacy sales in EUR',
        'E-commerce sales B2C in EUR',
        'E-commerce sales B2B in EUR',
        'Third party in EUR',
        'Other in EUR',
        'Total sales in EUR'
    ]);

    // Wypełnienie wierszy kanałów
    if (!empty($data['channels'])) {
        foreach ($data['channels'] as $channelName => $channelData) {
            $row = [
                $userIndex, // Distributor = indeks twórcy
                'PLN',      // Currency
                $channelData['pln'] ?? '', // PLN
                '', // Pharmacy sales (możesz dodać jeśli masz mapping po nazwach kanałów)
                '', // E-commerce B2C
                '', // E-commerce B2B
                '', // Third party
                '', // Other
                $channelData['pln'] ?? '', // Total sales
                count($channelData['clients'] ?? []), // New clients
                $channelData['eur'] ?? '', // Professional sales in EUR
                '', '', '', '', '', '', $channelData['eur'] ?? '' // pozostałe EUR + total
            ];

            // Mapowanie po nazwach kanałów jeśli chcesz je wstawiać dokładnie:
            switch ($channelName) {
                case "Professional Sales":
                    $row[2] = $channelData['pln'] ?? '';
                    $row[10] = $channelData['eur'] ?? '';
                    break;
                case "Pharmacy Sales":
                    $row[3] = $channelData['pln'] ?? '';
                    $row[11] = $channelData['eur'] ?? '';
                    break;
                case "E-commerce Sales B2C":
                    $row[4] = $channelData['pln'] ?? '';
                    $row[12] = $channelData['eur'] ?? '';
                    break;
                case "E-commerce Sales B2B":
                    $row[5] = $channelData['pln'] ?? '';
                    $row[13] = $channelData['eur'] ?? '';
                    break;
                case "Third Party":
                    $row[6] = $channelData['pln'] ?? '';
                    $row[14] = $channelData['eur'] ?? '';
                    break;
                case "Other":
                    $row[7] = $channelData['pln'] ?? '';
                    $row[15] = $channelData['eur'] ?? '';
                    break;
            }
            // Total Sales i Total Sales EUR już są w kolumnach 8 i 16

            fputcsv($fp, $row);
        }
    }

    fclose($fp);

    echo json_encode([
        'success' => true,
        'file_path' => str_replace(__DIR__ . '/', '', $filepath),
        'file_name' => $filename
    ]);
?>
