<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization");

// Menggunakan `require_once` untuk memastikan file hanya dimuat sekali
require_once '../database/db.php';
require_once '../models/riwayat.php';

// Pastikan $pdo tersedia dari file db.php
if (!isset($pdo)) {
    echo json_encode(["message" => "Database connection not initialized."]);
    exit;
}

// Buat instance Riwayat
$riwayat = new Riwayat($pdo);

// Menentukan method HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $data = $riwayat->getAll();
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['customer_name']) || empty($input['menu_name']) || empty($input['quantity']) || empty($input['total_price']) || empty($input['table_number'])) {
            echo json_encode(["message" => "Data tidak lengkap."]);
            break;
        }

        if ($riwayat->create($input)) {
            echo json_encode(["message" => "Riwayat berhasil ditambahkan."]);
        } else {
            echo json_encode(["message" => "Gagal menambahkan riwayat."]);
        }
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(["message" => "ID riwayat tidak ditemukan."]);
            break;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if ($riwayat->update($id, $input)) {
            echo json_encode(["message" => "Riwayat berhasil diperbarui."]);
        } else {
            echo json_encode(["message" => "Gagal memperbarui riwayat."]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(["message" => "ID riwayat tidak ditemukan."]);
            break;
        }

        if ($riwayat->delete($id)) {
            echo json_encode(["message" => "Riwayat berhasil dihapus."]);
        } else {
            echo json_encode(["message" => "Gagal menghapus riwayat."]);
        }
        break;

    default:
        echo json_encode(["message" => "Metode tidak valid."]);
        break;
}
?>
