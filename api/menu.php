<?php
require_once '../models/menu.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Menampilkan semua produk
        header('Content-Type: application/json');
        echo json_encode(getProducts());
        break;

    case 'POST':
        // Menambahkan produk baru
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['nama']) && !empty($data['kategori']) && !empty($data['harga'])) {
            $status = isset($data['status']) ? $data['status'] : 'Tersedia';
            addProduct($data['nama'], $data['kategori'], $data['harga'], $status);
            echo json_encode(["message" => "Produk berhasil ditambahkan"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;



        case 'PUT':
            // Mengupdate produk
            $data = json_decode(file_get_contents("php://input"), true);
        
            if (!empty($data['id']) && !empty($data['nama']) && !empty($data['kategori']) && !empty($data['harga']) && !empty($data['status'])) {
                $isUpdated = updateProduct($data['id'], $data['nama'], $data['kategori'], $data['harga'], $data['status']);
                
                if ($isUpdated) {
                    echo json_encode(["message" => "Produk berhasil diperbarui"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Produk dengan ID tersebut tidak ditemukan"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap"]);
            }
            break;
        


        case 'DELETE':
            // Ambil ID dari query parameter atau body JSON
            $data = json_decode(file_get_contents("php://input"), true);
            $id = isset($_GET['id']) ? intval($_GET['id']) : ($data['id'] ?? null);
        
            if ($id) {
                $isDeleted = deleteProduct($id);
        
                if ($isDeleted) {
                    echo json_encode(["message" => "Produk berhasil dihapus"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Produk dengan ID tersebut tidak ditemukan"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "ID produk tidak ditemukan dalam permintaan"]);
            }
            break;        
}
?>
