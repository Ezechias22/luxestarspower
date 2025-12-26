<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/Database.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode(['products' => []]);
    exit;
}

try {
    $productRepo = new \App\Repositories\ProductRepository();
    $result = $productRepo->getAllPaginated(1, 5, ['search' => $query]);
    
    echo json_encode(['products' => $result['data'] ?? []]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}