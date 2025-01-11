<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

if ($num > 0) {
    $kategori_arr = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kategori_item = [
            "id_kategori" => $row["id_kategori"],
            "jenis_kategori" => $row["jenis_kategori"],
            "nama_kategori" => $row["nama_kategori"],
            "dibuat_pada" => $row["dibuat_pada"],
            "diperbarui_pada" => $row["diperbarui_pada"]
        ];
        array_push($kategori_arr, $kategori_item);
    }

    http_response_code(200);
    echo json_encode($kategori_arr);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Kategori tidak ditemukan."]);
}
