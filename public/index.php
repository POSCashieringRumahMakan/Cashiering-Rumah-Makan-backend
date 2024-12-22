<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tangani preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/Member.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'POST') {
    // Registrasi (POST /registrasi)
    if (strpos($uri, '/registrasi') !== false && strpos($uri, '/check-email') === false) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->nama) || empty($data->email) || empty($data->password)) {
            echo json_encode(['message' => 'Semua field harus diisi!']);
            http_response_code(400);
            exit;
        }

        $member = new Member();
        if ($member->checkEmail($data->email)) {
            echo json_encode(['message' => 'Email sudah terdaftar.']);
            http_response_code(400);
            exit;
        }

        if ($member->create($data->nama, $data->email, $data->password)) {
            echo json_encode(['message' => 'Registrasi berhasil!']);
            http_response_code(201);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    }

    // Cek email (POST /registrasi/check-email)
    if (strpos($uri, '/registrasi/check-email') !== false) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email)) {
            echo json_encode(['message' => 'Email harus diisi!']);
            http_response_code(400);
            exit;
        }

        $member = new Member();
        if ($member->checkEmail($data->email)) {
            echo json_encode(['message' => 'Email sudah terdaftar.']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Email tersedia.']);
            http_response_code(200);
        }
    }

    // Login (POST /login)
    if (strpos($uri, '/login') !== false) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email) || empty($data->password)) {
            echo json_encode(['message' => 'Email dan password harus diisi!']);
            http_response_code(400);
            exit;
        }

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
