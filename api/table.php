<?php
require_once '../models/Table.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$table = new Table();

switch ($method) {
    case 'GET':
        // Menampilkan semua meja
        header('Content-Type: application/json');
        echo json_encode($table->getAllTables());
        break;

    case 'POST':
        // Menambahkan meja baru dengan status 'tersedia' jika tidak ada status yang diberikan
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->number) && !empty($data->capacity)) {
            // Jika status tidak diberikan, defaultkan ke 'tersedia'
            $status = isset($data->status) ? $data->status : 'tersedia';
            $table->addTable($data->number, $data->capacity, $status);
            echo json_encode(["message" => "Meja berhasil ditambahkan dengan status '$status'"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'PUT':
        // Mengupdate meja
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->number) && !empty($data->capacity) && !empty($data->status)) {
            $table->updateTable($data->id, $data->number, $data->capacity, $data->status);
            echo json_encode(["message" => "Meja berhasil diperbarui"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'DELETE':
        // Menghapus meja
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $table->deleteTable($data->id);
            echo json_encode(["message" => "Meja berhasil dihapus"]);
        } else {
            echo json_encode(["message" => "ID meja tidak ditemukan"]);
        }
        break;

    default:
        echo json_encode(["message" => "Metode tidak ditemukan"]);
        break;
}
?>
