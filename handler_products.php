<?php
    require_once "config.php";
    header('Content-Type: application/json');
    error_reporting(0);

    try {
        $stmt = $pdo->query("SELECT id_product, sku, name, description FROM products ORDER BY name ASC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } catch (Exception $e) {
        echo json_encode(["error" => "Błąd pobierania produktów: " . $e->getMessage()]);
    }
?>