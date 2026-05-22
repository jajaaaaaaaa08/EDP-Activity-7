<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'database.php';
require_once 'includes/reports.php';

$database = new Database();
$conn = $database->connect();

$report_type = $_POST['report_type'] ?? 'Inventory Report';
$export = buildExportPayload($conn, $report_type);

file_put_contents($export['data_file'], json_encode($export['data']));

$python_path = 'C:\\Users\\admin\\AppData\\Local\\Python\\pythoncore-3.14-64\\python.exe';
exec("\"$python_path\" export_engine.py \"{$export['data_file']}\" \"{$export['output_file']}\" 2>&1", $output);

if (file_exists($export['output_file'])) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . pathinfo($export['output_file'], PATHINFO_FILENAME) . '_' . date('Y-m-d') . '.xlsx"');
    readfile($export['output_file']);
    unlink($export['data_file']);
    unlink($export['output_file']);
    exit;
}

if (file_exists($export['data_file'])) {
    unlink($export['data_file']);
}

die('Error generating report: ' . implode("\n", $output));
