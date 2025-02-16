<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/kasir.php';

$database = new Database();
$db = $database->getConnection();

$kasir = new Kasir($db);

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        $stmt = $kasir->getAllOrders();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($orders);
        break;
    
    case 'POST':
        if ($kasir->addOrder($data)) {
            echo json_encode(["message" => "Pesanan berhasil ditambahkan"]);
        } else {
            echo json_encode(["message" => "Gagal menambahkan pesanan"]);
        }
        break;
    
    case 'PUT':
        if ($kasir->updateOrder($data)) {
            echo json_encode(["message" => "Pesanan berhasil diperbarui"]);
        } else {
            echo json_encode(["message" => "Gagal memperbarui pesanan"]);
        }
        break;
    
    case 'DELETE':
        if ($kasir->deleteOrder($data['id'])) {
            echo json_encode(["message" => "Pesanan berhasil dihapus"]);
        } else {
            echo json_encode(["message" => "Gagal menghapus pesanan"]);
        }
        break;
    
    default:
        echo json_encode(["message" => "Metode tidak diizinkan"]);
        break;
}
