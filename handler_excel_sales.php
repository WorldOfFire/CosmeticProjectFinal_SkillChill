<?php
require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excelFile'])) {
    echo json_encode(["success" => false, "message" => "Nie przesłano pliku Excel."]);
    exit;
}

$file = $_FILES['excelFile'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Błąd podczas przesyłania pliku."]);
    exit;
}

// Wyciągnięcie kwartału i roku z nazwy pliku
$filename = $file['name'];
if (preg_match('/Q([1-4])[_-]?(\d{4})/', $filename, $matches)) {
    $quarter = $matches[1];
    $year = $matches[2];
} else {
    echo json_encode(["success" => false, "message" => "Nie udało się wyciągnąć kwartału i roku z nazwy pliku."]);
    exit;
}

// Lista arkuszy do pobrania
$sheetNames = [
    "Professional Sales",
    "Pharmacy Sales",
    "E-commerce Sales B2C",
    "E-commerce Sales B2B",
    "Third Party",
    "Other",
    "Stan magazynu"
];

try {
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $allData = [];
    $inventoryData = [];

    foreach ($sheetNames as $sheetName) {
        if ($spreadsheet->sheetNameExists($sheetName)) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $data = $sheet->toArray();
            $data = array_filter($data, fn($row) => !empty(array_filter($row)));
            $data = array_values($data);

            if ($sheetName === "Stan magazynu") {
                $inventoryData = $data;
            } else {
                $allData[$sheetName] = $data;
            }
        } else {
            if ($sheetName === "Stan magazynu") $inventoryData = [];
            else $allData[$sheetName] = [];
        }
    }

    echo json_encode([
        "success" => true,
        "quarter" => $quarter,
        "year" => $year,
        "sheetsData" => $allData,
        "inventoryData" => $inventoryData
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Błąd przy odczycie pliku Excel: " . $e->getMessage()
    ]);
}
