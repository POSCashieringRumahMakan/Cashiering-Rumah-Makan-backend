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
        $lastId = $pdo->lastInsertId(); // Ambil ID yang baru saja dibuat
        echo json_encode([
            'message' => 'Check-in berhasil!',
            'data' => [
                'id' => $lastId, // Kirim ID ke frontend
                'pengguna_id' => $data->pengguna_id,
                'tanggal' => $data->tanggal,
                'jam_masuk' => $data->jam_masuk
            ]
        ]);
        http_response_code(201);
    }
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->pengguna_id) || empty($data->jam_keluar)) {
        echo json_encode(['message' => 'Pengguna ID dan jam keluar harus diisi!']);
        http_response_code(400);
        exit;
    }

    if ($absensi->checkOut($data->pengguna_id, $data->jam_keluar)) {
        echo json_encode(['message' => 'Check-out berhasil!']);
        http_response_code(200);
    } else {
        echo json_encode(['message' => 'Gagal check-out, mungkin sudah check-out sebelumnya.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if (isset($_GET['pengguna_id'])) {
        echo json_encode($absensi->getAllByUser($_GET['pengguna_id']));
        http_response_code(200);
    } elseif ($id) {
        $result = $absensi->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Data absensi tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        // Ambil semua data jika tidak ada parameter
        echo json_encode($absensi->getAll());
        http_response_code(200);
    }    
}

