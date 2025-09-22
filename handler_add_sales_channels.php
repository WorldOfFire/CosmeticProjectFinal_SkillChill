<?php
session_start();
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

// --- Funkcja do pobrania kursu EUR z NBP ---
function getEurRate() {
    try {
        $url = "https://api.nbp.pl/api/exchangerates/rates/a/eur/?format=json";
        $data = file_get_contents($url);
        if (!$data) throw new Exception("Brak danych z NBP");
        $json = json_decode($data, true);
        return floatval($json['rates'][0]['mid'] ?? 4.5);
    } catch (Exception $e) {
        return 4.5; // Domyślny kurs, gdy API nie odpowiada
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metoda nieobsługiwana.");
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        throw new Exception("Niepoprawny JSON.");
    }

    $eurRate = getEurRate();

    $pdo->beginTransaction();

    // --- 1. Raport ---
    $stmt = $pdo->prepare("
        INSERT INTO report 
        (quarter, year, total_sales_pl, total_sales_eur, 
         name_creator, surname_creator, login_creator, index_creator, created_at)
        VALUES 
        (:quarter, :year, :pl, :eur, 
         :name, :surname, :login, :idx, NOW())
    ");
    $stmt->execute([
        ':quarter' => $data['quarter'],
        ':year' => $data['year'],
        ':pl' => $data['total_sales_pl'],
        ':eur' => round($data['total_sales_pl'] / $eurRate, 2),
        ':name' => $_SESSION['user_name'],
        ':surname' => $_SESSION['user_surname'],
        ':login' => $_SESSION['user'],
        ':idx' => $_SESSION['user_index']
    ]);
    $reportId = $pdo->lastInsertId();

    // --- 2. Kanały sprzedaży ---
    foreach ($data['channels'] as $chName => $chData) {
        // Pobranie ID kanału
        $stmt = $pdo->prepare("SELECT id_sales_channel FROM sales_channels_name WHERE sale_channel_name = :name LIMIT 1");
        $stmt->execute([':name' => $chName]);
        $channelId = $stmt->fetchColumn();
        if (!$channelId) throw new Exception("❌ Kanał sprzedaży nie istnieje: $chName");

        // Wstawienie sprzedaży kanału
        $stmt = $pdo->prepare("
            INSERT INTO sales_channels (report_id, sales_channel_id, sale_pln, sale_eur)
            VALUES (:report, :channel, :pln, :eur)
        ");
        $stmt->execute([
            ':report' => $reportId,
            ':channel' => $channelId,
            ':pln' => $chData['pln'],
            ':eur' => round($chData['pln'] / $eurRate, 2)
        ]);
        $salesChannelId = $pdo->lastInsertId();

        // Produkty w kanale
        if (!empty($chData['products'])) {
            foreach ($chData['products'] as $p) {
                $stmt = $pdo->prepare("SELECT id_product FROM products WHERE sku = :sku LIMIT 1");
                $stmt->execute([':sku' => $p['sku']]);
                $productId = $stmt->fetchColumn();
                if ($productId) {
                    $stmt = $pdo->prepare("
                        INSERT INTO channels_products 
                        (product_id, channels_id, month, quantity_sold, sales_value_pln, sales_value_eur)
                        VALUES (:pid, :cid, :month, :qty, :pln, :eur)
                    ");
                    $stmt->execute([
                        ':pid' => $productId,
                        ':cid' => $salesChannelId,
                        ':month' => $p['month'],
                        ':qty' => $p['quantity'],
                        ':pln' => $p['value'] ?? 0,
                        ':eur' => round(($p['value'] ?? 0) / $eurRate, 2)
                    ]);
                }
            }
        }

        // Klienci w kanale
        if (!empty($chData['clients'])) {
            foreach ($chData['clients'] as $c) {
                $cd = $c['data'] ?? [];
                if ($c['type'] === 'individual') {
                    $stmt = $pdo->prepare("
                        INSERT INTO clients_individual 
                        (report_id, client_name, client_surname, client_address, phone_nr, client_mail)
                        VALUES (:rid, :name, :surname, :addr, :phone, :email)
                    ");
                    $stmt->execute([
                        ':rid' => $reportId,
                        ':name' => $cd['name'] ?? '',
                        ':surname' => $cd['surname'] ?? '',
                        ':addr' => $cd['address'] ?? '',
                        ':phone' => $cd['phone'] ?? '',
                        ':email' => $cd['email'] ?? ''
                    ]);
                } elseif ($c['type'] === 'company') {
                    $stmt = $pdo->prepare("
                        INSERT INTO entrepreneur 
                        (report_id, owner_name, owner_surname, company_name, nip, regon, company_address, phone_nr, company_mail)
                        VALUES (:rid, :oname, :osurname, :cname, :nip, :regon, :addr, :phone, :email)
                    ");
                    $stmt->execute([
                        ':rid' => $reportId,
                        ':oname' => $cd['name'] ?? '',
                        ':osurname' => $cd['surname'] ?? '',
                        ':cname' => $cd['company'] ?? '',
                        ':nip' => $cd['nip'] ?? '',
                        ':regon' => $cd['regon'] ?? '',
                        ':addr' => $cd['address'] ?? '',
                        ':phone' => $cd['phone'] ?? '',
                        ':email' => $cd['email'] ?? ''
                    ]);
                } elseif ($c['type'] === 'corporation') {
                    $stmt = $pdo->prepare("
                        INSERT INTO company_corporate_entity 
                        (report_id, company_name, legal_form, nip, regon, krs, legal_address, contact_person, contact_mail, contact_phone_nr)
                        VALUES (:rid, :cname, :form, :nip, :regon, :krs, :addr, :person, :email, :phone)
                    ");
                    $stmt->execute([
                        ':rid' => $reportId,
                        ':cname' => $cd['company'] ?? '',
                        ':form' => $cd['entity_type'] ?? '',
                        ':nip' => $cd['nip'] ?? '',
                        ':regon' => $cd['regon'] ?? '',
                        ':krs' => $cd['krs'] ?? '',
                        ':addr' => $cd['address'] ?? '',
                        ':person' => $cd['contact_person'] ?? '',
                        ':email' => $cd['email'] ?? '',
                        ':phone' => $cd['phone'] ?? ''
                    ]);
                }
            }
        }
    }

    // --- 3. Stan magazynu ---
    if (!empty($data['inventory'])) {
        foreach ($data['inventory'] as $inv) {
            $hasData = false;
            foreach (['initial_stock','delivery','sold_quantity'] as $field) {
                if (isset($inv[$field]) && $inv[$field] != 0) {
                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) continue;

            $stmt = $pdo->prepare("SELECT id_product FROM products WHERE sku = :sku LIMIT 1");
            $stmt->execute([':sku' => $inv['product_id']]);
            $productId = $stmt->fetchColumn();

            if ($productId) {
                $initial = $inv['initial_stock'] ?? 0;
                $delivery = $inv['delivery'] ?? 0;
                $sold = $inv['sold_quantity'] ?? 0;
                $remaining = $initial + $delivery - $sold;

                if ($sold > ($initial + $delivery)) {
                    throw new Exception("Produkt SKU {$inv['product_id']}: sprzedano więcej sztuk niż dostępne w magazynie.");
                }

                $stmt = $pdo->prepare("
                    INSERT INTO inventory (report_id, product_id, initial_stock, delivery, sold_quantity, remaining)
                    VALUES (:rid, :pid, :init, :del, :sold, :rem)
                ");
                $stmt->execute([
                    ':rid' => $reportId,
                    ':pid' => $productId,
                    ':init' => $initial,
                    ':del' => $delivery,
                    ':sold' => $sold,
                    ':rem' => $remaining
                ]);
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "report_id" => $reportId
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode([
        "success" => false,
        "message" => "Błąd przy zapisie: " . $e->getMessage()
    ]);
}
