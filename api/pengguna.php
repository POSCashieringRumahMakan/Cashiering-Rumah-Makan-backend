<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/pengguna.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$pengguna = new Pengguna();

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nama) || empty($data->email) || empty($data->noTelepon) || empty($data->tingkatan)) {
        echo json_encode(['message' => 'Semua field harus diisi!']);
        http_response_code(400);
        exit;
    }

    if ($pengguna->create($data->nama, $data->email, $data->noTelepon, $data->tingkatan)) {
        echo json_encode(['message' => 'Pengguna berhasil ditambahkan!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if ($id) {
        $result = $pengguna->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Pengguna tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        echo json_encode($pengguna->getAll());
        http_response_code(200);
    }
}

if ($method === 'PUT') {
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->nama) || empty($data->email) || empty($data->noTelepon) || empty($data->tingkatan)) {
            echo json_encode(['message' => 'Semua field harus diisi!']);
            http_response_code(400);
            exit;
        }

        if ($pengguna->update($id, $data->nama, $data->email, $data->noTelepon, $data->tingkatan)) {
            echo json_encode(['message' => 'Pengguna berhasil diperbarui!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pengguna tidak ditemukan.']);
        http_response_code(400);
    }
}

if ($method === 'DELETE') {
    if ($id) {
        if ($pengguna->delete($id)) {
            echo json_encode(['message' => 'Pengguna berhasil dihapus!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pengguna tidak ditemukan.']);
        http_response_code(400);
    }
}
?>
