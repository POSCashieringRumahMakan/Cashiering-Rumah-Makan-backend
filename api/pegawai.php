<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tangani preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/pegawai.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$pegawai = new Pegawai();

if ($method === 'POST') {
    // Tambah pegawai
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nama) || empty($data->jabatan) || empty($data->email) || empty($data->no_telepon) || empty($data->status)) {
        echo json_encode(['message' => 'Semua field harus diisi!']);
        http_response_code(400);
        exit;
    }

    if ($pegawai->create($data->nama, $data->jabatan, $data->email, $data->no_telepon, $data->status)) {
        echo json_encode(['message' => 'Pegawai berhasil ditambahkan!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if ($id) {
        // Ambil pegawai berdasarkan ID
        $result = $pegawai->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Pegawai tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        // Ambil semua pegawai
        echo json_encode($pegawai->getAll());
        http_response_code(200);
    }
}

if ($method === 'PUT') {
    // Ubah pegawai
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        // Periksa apakah semua field sudah diisi
        if (empty($data->nama) || empty($data->jabatan) || empty($data->email) || empty($data->no_telepon) || empty($data->status)) {
            echo json_encode(['message' => 'Semua field harus diisi!']);
            http_response_code(400);
            exit;
        }

        // Cek apakah data pegawai berhasil diperbarui
        if ($pegawai->update($id, $data->nama, $data->jabatan, $data->email, $data->no_telepon, $data->status)) {
            echo json_encode(['message' => 'Pegawai berhasil diperbarui!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pegawai tidak ditemukan.']);
        http_response_code(400);
    }
}


if ($method === 'DELETE') {
    // Hapus pegawai
    if ($id) {
        if ($pegawai->delete($id)) {
            echo json_encode(['message' => 'Pegawai berhasil dihapus!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pegawai tidak ditemukan.']);
        http_response_code(400);
    }
}
?>
