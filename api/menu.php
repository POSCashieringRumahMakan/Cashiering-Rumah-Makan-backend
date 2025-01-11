<?php
require_once '../models/Table.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$table = new Table();

switch ($method) {
    case 'GET':
        // Menampilkan semua produk
        header('Content-Type: application/json');
        echo json_encode($table->getAllTables());
        break;

    case 'POST':
        // Menambahkan produk baru dengan status default 'tersedia' jika tidak ada status yang diberikan
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->nama) && !empty($data->kategori) && !empty($data->harga)) {
            // Jika status tidak diberikan, defaultkan ke 'tersedia'
            $status = isset($data->status) ? $data->status : 'tersedia';
            $table->addProduct($data->nama, $data->kategori, $data->harga, $status);
            echo json_encode(["message" => "Produk berhasil ditambahkan dengan status '$status'"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'PUT':
        // Mengupdate produk
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->nama) && !empty($data->kategori) && !empty($data->harga) && !empty($data->status)) {
            $table->updateProduct($data->id, $data->nama, $data->kategori, $data->harga, $data->status);
            echo json_encode(["message" => "Produk berhasil diperbarui"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'DELETE':
        // Menghapus produk
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $table->deleteProduct($data->id);
            echo json_encode(["message" => "Produk berhasil dihapus"]);
        } else {
            echo json_encode(["message" => "ID produk tidak ditemukan"]);
        }
        break;

    default:
        echo json_encode(["message" => "Metode tidak ditemukan"]);
        break;
}
?>
