<?php
session_start();
header('Content-Type: text/plain');

include "config.php";

// --- Pobranie danych z POST ---
$user = $_POST['user'] ?? '';
$quarter = $_POST['quarter'] ?? '';
$year = $_POST['year'] ?? '';

$channels = json_decode($_POST['channels'] ?? '[]', true);
$clients = json_decode($_POST['clients'] ?? '[]', true);
$monthly_sales = json_decode($_POST['monthly_sales'] ?? '[]', true);
$inventory = json_decode($_POST['inventory'] ?? '[]', true);

// --- Walidacja minimalna ---
if (!$user || !$quarter || !$year) {
    echo "Brak podstawowych danych: user/quarter/year";
    exit;
}

try {
    $pdo->beginTransaction();

    // --- 1. Dodanie formularza ---
    $stmtForm = $pdo->prepare("
        INSERT INTO forms (created_at, user_id, first_name, last_name, user_index, user_login) 
        VALUES (NOW(), ?, ?, ?, ?, ?)
    ");
    $stmtForm->execute([
        $_SESSION['id_user'] ?? null,
        $_SESSION['user_name'] ?? null,
        $_SESSION['user_surname'] ?? null,
        $_SESSION['user_index'] ?? null,
        $_SESSION['user'] ?? null
    ]);

    $id_form = $pdo->lastInsertId();


    // --- 2. Kanały sprzedaży ---
    if (!empty($channels)) {
        $stmtChan = $pdo->prepare("INSERT INTO channel_sales (id_form, channel_name, sales_pln, sales_eur) VALUES (?, ?, ?, ?)");
        foreach ($channels as $ch) {
            if (!isset($ch['channel'], $ch['pln'], $ch['eur'])) continue;
            $stmtChan->execute([$id_form, $ch['channel'], $ch['pln'], $ch['eur']]);
        }
    }

    // --- 3. Nowi klienci ---
    if (!empty($clients)) {
        $stmtClient = $pdo->prepare("INSERT INTO new_clients (id_form, client_name, client_address, channel) VALUES (?, ?, ?, ?)");
        foreach ($clients as $c) {
            if (!isset($c['name'], $c['address'], $c['channel'])) continue;
            $stmtClient->execute([$id_form, $c['name'], $c['address'], $c['channel']]);
        }
    }

    // --- 4. Sprzedaż miesięczna ---
    if (!empty($monthly_sales)) {
        $stmtMonth = $pdo->prepare("INSERT INTO monthly_sales (id_form, month, sku, product_name, quantity, sales_pln, sales_eur, quarter, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($monthly_sales as $m) {
            if (!isset($m['month'], $m['sku'], $m['product'], $m['qty'], $m['pln'], $m['eur'], $m['quarter'], $m['year'])) continue;
            $stmtMonth->execute([
                $id_form,
                $m['month'],
                $m['sku'],
                $m['product'],
                $m['qty'],
                $m['pln'],
                $m['eur'],
                $m['quarter'],
                $m['year']
            ]);
        }
    }

    // --- 5. Stan magazynu ---
    if (!empty($inventory)) {
        $stmtInv = $pdo->prepare("INSERT INTO inventory (id_form, sku, product_name, initial_stock, received_stock, sold, final_stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($inventory as $inv) {
            if (!isset($inv['sku'], $inv['product'], $inv['initial'], $inv['received'], $inv['sold'], $inv['final'])) continue;
            $stmtInv->execute([
                $id_form,
                $inv['sku'],
                $inv['product'],
                $inv['initial'],
                $inv['received'],
                $inv['sold'],
                $inv['final']
            ]);
        }
    }

    $pdo->commit();
    echo "Raport zapisany poprawnie!";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Błąd zapisu danych: " . $e->getMessage();
}
