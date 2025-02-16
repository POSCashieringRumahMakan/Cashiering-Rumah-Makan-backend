<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tangani preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/absensi.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$absensi = new Absensi();

if ($method === 'POST') {
    // Tambah absensi (Check-in)
    $data = json_decode(file_get_contents("php://input"));
    error_log(json_encode($data)); // Menampilkan data JSON untuk debugging

    if (empty($data->pengguna_id) || empty($data->tanggal) || empty($data->jam_masuk)) {
        echo json_encode(['message' => 'Semua field harus diisi!']);
        http_response_code(400);
        exit;
    }

    if ($absensi->checkIn($data->pengguna_id, $data->tanggal, $data->jam_masuk)) {
        echo json_encode(['message' => 'Check-in berhasil!', 'data' => $data]);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.', 'error' => $pdo->errorInfo()]);
        http_response_code(500);
    }
}

if ($method === 'PUT') {
    // Update absensi (Check-out)
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->jam_keluar)) {
            echo json_encode(['message' => 'Jam keluar harus diisi!']);
            http_response_code(400);
            exit;
        }

        if ($absensi->checkOut($id, $data->jam_keluar)) {
            echo json_encode(['message' => 'Check-out berhasil!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID absensi tidak ditemukan.']);
        http_response_code(400);
    }
}

if ($method === 'GET') {
    if ($id) {
        // Ambil absensi berdasarkan ID
        $result = $absensi->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Data absensi tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        // Ambil semua data absensi
        echo json_encode($absensi->getAll());
        http_response_code(200);
    }
}
