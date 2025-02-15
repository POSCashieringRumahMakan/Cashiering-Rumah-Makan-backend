<?php
require_once __DIR__ . '/../models/menu.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        header('Content-Type: application/json');
        echo json_encode(getProducts());
        break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!empty($data['nama']) && !empty($data['id_kategori']) && !empty($data['harga'])) {
                $status = isset($data['status']) ? $data['status'] : 'tersedia';
                $newId = addProduct($data['nama'], $data['id_kategori'], $data['harga'], $status);
                
                // Ambil data produk yang baru ditambahkan
                $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
                $stmt->execute(['id' => $newId]);
                $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);
        
                echo json_encode([
                    "message" => "Produk berhasil ditambahkan",
                    "data" => $newProduct
                ]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap"]);
            }
            break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['id']) && !empty($data['nama']) && !empty($data['id_kategori']) && !empty($data['harga']) && isset($data['status'])) {
            $isUpdated = updateProduct($data['id'], $data['nama'], $data['id_kategori'], $data['harga'], $data['status']);
            echo json_encode(["message" => $isUpdated ? "Produk berhasil diperbarui" : "Produk dengan ID tersebut tidak ditemukan"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : ($data['id'] ?? null);
        if ($id) {
            $isDeleted = deleteProduct($id);
            echo json_encode(["message" => $isDeleted ? "Produk berhasil dihapus" : "Produk dengan ID tersebut tidak ditemukan"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID produk tidak ditemukan dalam permintaan"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Metode tidak diizinkan"]);
        break;
}
?>
