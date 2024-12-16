<?php

header('Content-Type: application/json');
require_once '../models/Member.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Cek endpoint API
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Registrasi (POST /registrasi)
    if (strpos($uri, '/registrasi') !== false) {
        // Ambil data dari body request
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->nama) || empty($data->email) || empty($data->password)) {
            echo json_encode(['message' => 'Semua field harus diisi!']);
            http_response_code(400);
            exit;
        }

        // Cek apakah email sudah terdaftar
        $member = new Member();
        if ($member->checkEmail($data->email)) {
            echo json_encode(['message' => 'Email sudah terdaftar.']);
            http_response_code(400);
            exit;
        }

        // Proses registrasi
        if ($member->create($data->nama, $data->email, $data->password)) {
            echo json_encode(['message' => 'Registrasi berhasil!']);
            http_response_code(201);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    }

    // Login (POST /login)
    if (strpos($uri, '/login') !== false) {
        // Ambil data dari body request
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email) || empty($data->password)) {
            echo json_encode(['message' => 'Email dan password harus diisi!']);
            http_response_code(400);
            exit;
        }

        // Proses login
        $member = new Member();
        $user = $member->login($data->email, $data->password);

        if ($user) {
            echo json_encode([
                'message' => 'Login berhasil!',
                'user' => $user
            ]);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Email atau password salah.']);
            http_response_code(401);
        }
    }
} else {
    echo json_encode(['message' => 'Metode request tidak diizinkan.']);
    http_response_code(405);
}
?>
