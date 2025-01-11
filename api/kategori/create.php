<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->jenis_kategori) && !empty($data->nama_kategori)) {
    $query = "INSERT INTO kategori (jenis_kategori, nama_kategori) VALUES (:jenis_kategori, :nama_kategori)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":jenis_kategori", $data->jenis_kategori);
    $stmt->bindParam(":nama_kategori", $data->nama_kategori);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Kategori berhasil ditambahkan."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menambahkan kategori."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap."]);
}
