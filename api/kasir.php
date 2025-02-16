<?php
require_once '../database/db.php';
require_once '../models/kasir.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$kasir = new Kasir($pdo);

$method = $_SERVER['REQUEST_METHOD'];

// ✅ Handle preflight request
if ($method == 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        // Ambil semua transaksi atau berdasarkan ID tertentu
        $id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : null;

        try {
            if ($id_transaksi) {
                $result = $kasir->getTransaksiById($id_transaksi);
            } else {
                $result = $kasir->getAllTransaksi();
            }

            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal mengambil transaksi", "message" => $e->getMessage()]);
        }
        break;
        
case 'POST':
    // Ambil JSON input dan bersihkan karakter tersembunyi
    $inputJSON = file_get_contents("php://input");
    $cleanedJSON = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $inputJSON));
    
    $data = json_decode($cleanedJSON, true);

    // Debugging jika JSON tidak valid
    if ($data === null) {
        http_response_code(400);
        echo json_encode([
            "error" => "Gagal membaca input JSON!",
            "input" => $cleanedJSON,
            "decoded" => json_last_error_msg()
        ]);
        exit;
    }

    // Validasi data yang diperlukan
    if (!isset($data['nama_pengguna'], $data['table_id'], $data['metode_pembayaran'], $data['menu']) 
        || !is_array($data['menu']) 
        || empty($data['menu'])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap! Pastikan semua field dikirim dengan benar."]);
        exit;
    }

    // Ambil nilai dari JSON
    $nama_pengguna = $data['nama_pengguna'];
    $table_id = $data['table_id'];
    $metode_pembayaran = $data['metode_pembayaran'];
    $menu = $data['menu'];

    try {
        // Mulai transaksi database
        $pdo->beginTransaction();

        // **Hitung total harga transaksi**
        $total_harga = 0;

        // Periksa setiap menu_id dan ambil harganya dari database
        foreach ($menu as $item) {
            if (!isset($item['menu_id'], $item['kuantitas'])) {
                throw new Exception("Data menu tidak valid!");
            }

            // Ambil harga menu dari tabel menu
            $stmtHarga = $pdo->prepare("SELECT harga FROM menu WHERE id = :menu_id");
            $stmtHarga->execute([':menu_id' => trim($item['menu_id'])]);
            $menuData = $stmtHarga->fetch(PDO::FETCH_ASSOC);

            // Jika menu tidak ditemukan, batalkan transaksi
            if (!$menuData) {
                throw new Exception("Menu ID " . $item['menu_id'] . " tidak ditemukan!");
            }

            // Hitung total harga
            $total_harga += $menuData['harga'] * $item['kuantitas'];
        }

        // **Simpan transaksi utama ke tabel `transaksi`**
        $stmt = $pdo->prepare("
            INSERT INTO transaksi (nama_pengguna, table_id, metode_pembayaran, total_harga, created_at)
            VALUES (:nama_pengguna, :table_id, :metode_pembayaran, :total_harga, NOW())
        ");
        $stmt->execute([
            ':nama_pengguna' => $nama_pengguna,
            ':table_id' => $table_id,
            ':metode_pembayaran' => $metode_pembayaran,
            ':total_harga' => $total_harga
        ]);

        // Ambil ID transaksi yang baru dibuat
        $transaksi_id = $pdo->lastInsertId();

        // **Simpan setiap item menu ke dalam tabel `transaksi_detail`**
        $stmtDetail = $pdo->prepare("
            INSERT INTO transaksi_detail (transaksi_id, menu_id, kuantitas)
            VALUES (:transaksi_id, :menu_id, :kuantitas)
        ");

        foreach ($menu as $item) {
            $stmtDetail->execute([
                ':transaksi_id' => $transaksi_id,
                ':menu_id' => $item['menu_id'],
                ':kuantitas' => $item['kuantitas']
            ]);
        }

        // Commit transaksi ke database
        $pdo->commit();

        http_response_code(201);
        echo json_encode([
            "message" => "Transaksi berhasil dibuat",
            "transaksi_id" => $transaksi_id,
            "total_harga" => $total_harga
        ]);

    } catch (Exception $e) {
        $pdo->rollBack(); // Batalkan transaksi jika terjadi error
        http_response_code(500);
        echo json_encode(["error" => "Gagal membuat transaksi", "message" => $e->getMessage()]);
    }
    break;

        

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : ($data['id_transaksi'] ?? null);

        if (!$id_transaksi) {
            http_response_code(400);
            echo json_encode(["error" => "ID transaksi tidak ditemukan dalam permintaan"]);
            exit;
        }

        try {
            $kasir->deleteTransaksi($id_transaksi);
            echo json_encode(["message" => "Transaksi berhasil dihapus"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Gagal menghapus transaksi", "message" => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Metode tidak diizinkan"]);
        break;
}
?>
