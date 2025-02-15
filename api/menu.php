<?php
require_once __DIR__ . '/../models/menu.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
    switch ($_SERVER['REQUEST_METHOD']) {
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
            // Ambil data dari body request
            $data = json_decode(file_get_contents("php://input"), true);
    
            // Validasi data input
            if (!isset($data['nama']) || !isset($data['id_kategori']) || !isset($data['harga'])) {
                http_response_code(400);
                echo json_encode(["error" => "Data tidak lengkap"]);
                exit;
            }
    
            try {
                $status = isset($data['status']) ? $data['status'] : 'tersedia';
    
                // Mulai transaksi untuk memastikan data tersimpan
                $pdo->beginTransaction();
    
                // Query INSERT untuk menambahkan menu baru
                $stmt = $pdo->prepare("INSERT INTO menu (nama, id_kategori, harga, status) VALUES (:nama, :id_kategori, :harga, :status)");
                $stmt->execute([
                    'nama' => $data['nama'],
                    'id_kategori' => $data['id_kategori'],
                    'harga' => $data['harga'],
                    'status' => $status
                ]);
    
                // Ambil ID yang baru dimasukkan
                $newId = $pdo->lastInsertId();
    
                if (!$newId) {
                    throw new Exception("Gagal mendapatkan ID baru");
                }
    
                // Commit transaksi jika berhasil
                $pdo->commit();
    
                // Ambil data menu yang baru ditambahkan
                $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
                $stmt->execute(['id' => $newId]);
                $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
                http_response_code(201);
                echo json_encode([
                    "message" => "Produk berhasil ditambahkan",
                    "data" => $newProduct
                ]);
            } catch (Exception $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(["error" => "Gagal menambahkan produk", "message" => $e->getMessage()]);
            }
            break;
    

            case 'POST':
                header("Content-Type: application/json"); // Pastikan response dalam format JSON
            
                // Ambil data dari request body
                $data = json_decode(file_get_contents("php://input"), true);
            
                // Validasi data input
                if (!empty($data['nama']) && !empty($data['id_kategori']) && !empty($data['harga'])) {
                    try {
                        // Pastikan koneksi database ada
                        require_once 'database.php'; // Sesuaikan dengan lokasi koneksi database
            
                        // Gunakan nilai default untuk status jika tidak disertakan
                        $status = isset($data['status']) ? $data['status'] : 'tersedia';
            
                        // Query untuk menambahkan produk baru
                        $stmt = $pdo->prepare("INSERT INTO menu (nama, id_kategori, harga, status) VALUES (:nama, :id_kategori, :harga, :status)");
                        $stmt->execute([
                            'nama' => $data['nama'],
                            'id_kategori' => $data['id_kategori'],
                            'harga' => $data['harga'],
                            'status' => $status
                        ]);
            
                        // Ambil ID produk yang baru ditambahkan
                        $newId = $pdo->lastInsertId();
            
                        // Ambil data produk yang baru ditambahkan
                        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
                        $stmt->execute(['id' => $newId]);
                        $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);
            
                        // Kirim response sukses
                        http_response_code(201);
                        echo json_encode([
                            "message" => "Produk berhasil ditambahkan",
                            "data" => $newProduct
                        ]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(["error" => "Gagal menambahkan produk", "message" => $e->getMessage()]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Data tidak lengkap"]);
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
                        $stmt = $pdo->prepare("UPDATE menu SET nama = :nama, id_kategori = :id_kategori, harga = :harga, status = :status WHERE id = :id");
                        $stmt->execute([
                            'id' => $data['id'],
                            'nama' => $data['nama'],
                            'id_kategori' => $data['id_kategori'],
                            'harga' => $data['harga'],
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
