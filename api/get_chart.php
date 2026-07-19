<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$period = $_GET['period'] ?? '6bulan';
$data = store_read();

$allowed = ['6bulan', '3bulan', '1tahun'];
if (!in_array($period, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Periode tidak valid']);
    exit;
}

$series = $data['chart_series'][$period];

echo json_encode([
    'period' => $period,
    'labels' => $series['labels'],
    'values' => $series['values'],
]);
