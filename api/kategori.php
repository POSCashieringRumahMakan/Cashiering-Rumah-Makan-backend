<?php
header("Content-Type: application/json"); // Pastikan header content-type adalah JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/category.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$category = new Category();

if ($method === 'POST') {
    // Tambah kategori
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->jenis_kategori) || empty($data->nama_kategori)) {
        echo json_encode(['message' => 'Semua field harus diisi!']);
        http_response_code(400);
        exit;
    }

    if ($category->create($data->jenis_kategori, $data->nama_kategori)) {
        echo json_encode(['message' => 'Kategori berhasil ditambahkan!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if ($id) {
        // Ambil kategori berdasarkan ID
        $result = $category->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Kategori tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        // Ambil semua kategori
        $result = $category->getAll(); // Ambil semua kategori
        echo json_encode($result);
        http_response_code(200);
    }
}

// Jangan menambahkan teks atau pesan lain selain data JSON di sini
?>
