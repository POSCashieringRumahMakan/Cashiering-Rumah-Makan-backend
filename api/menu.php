<?php
require_once '../models/menu.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Membuat koneksi ke database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi berhasil!";
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal terhubung ke database", "message" => $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query("
                SELECT menu.id, menu.nama, menu.harga, menu.status, menu.id_kategori, kategori.nama_kategori 
                FROM menu 
                JOIN kategori ON menu.id_kategori = kategori.id_kategori
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$products) {
                echo json_encode(["message" => "Data menu kosong"]);
            } else {
                echo json_encode($products, JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal mengambil data produk", "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nama'], $data['id_kategori'], $data['harga']) || empty($data['nama']) || empty($data['id_kategori']) || empty($data['harga'])) {
            http_response_code(400);
            echo json_encode(["error" => "Data tidak lengkap"]);
            exit;
        }

        try {
            // Pastikan kategori ada di database
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE id_kategori = :id_kategori");
            $stmt->execute(['id_kategori' => (int) $data['id_kategori']]);
            $kategoriExists = $stmt->fetchColumn();

            if (!$kategoriExists) {
                http_response_code(400);
                echo json_encode(["error" => "Kategori dengan ID tersebut tidak ditemukan"]);
                exit;
            }

            // Tambahkan produk ke database
            $status = isset($data['status']) ? $data['status'] : 'tersedia';
            $stmt = $pdo->prepare("INSERT INTO menu (nama, id_kategori, harga, status) VALUES (:nama, :id_kategori, :harga, :status)");
            $stmt->execute([
                'nama' => $data['nama'],
                'id_kategori' => (int) $data['id_kategori'],
                'harga' => (float) $data['harga'],
                'status' => $status
            ]);

            $newId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
            $stmt->execute(['id' => $newId]);
            $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(201);
            echo json_encode(["message" => "Produk berhasil ditambahkan", "data" => $newProduct]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal menambahkan produk", "message" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['nama'], $data['id_kategori'], $data['harga'], $data['status'])) {
            http_response_code(400);
            echo json_encode(["error" => "Data tidak lengkap"]);
            exit;
        }

        try {
            // Cek apakah kategori dengan ID tersebut ada
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE id_kategori = :id_kategori");
            $stmt->execute(['id_kategori' => (int) $data['id_kategori']]);
            $kategoriExists = $stmt->fetchColumn();

            if (!$kategoriExists) {
                http_response_code(400);
                echo json_encode(["error" => "Kategori dengan ID tersebut tidak ditemukan"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE menu SET nama = :nama, id_kategori = :id_kategori, harga = :harga, status = :status WHERE id = :id");
            $stmt->execute([
                'id' => (int) $data['id'],
                'nama' => $data['nama'],
                'id_kategori' => (int) $data['id_kategori'],
                'harga' => (float) $data['harga'],
                'status' => $data['status']
            ]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "Produk berhasil diperbarui"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Produk dengan ID tersebut tidak ditemukan"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal memperbarui produk", "message" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $id = isset($_GET['id']) ? intval($_GET['id']) : ($data['id'] ?? null);

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID produk tidak ditemukan dalam permintaan"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "Produk berhasil dihapus"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Produk dengan ID tersebut tidak ditemukan"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal menghapus produk", "message" => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Metode tidak diizinkan"]);
        break;
}
?>
