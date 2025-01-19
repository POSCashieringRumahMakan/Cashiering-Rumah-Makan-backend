<?php
require_once '../database/db.php';

// Fungsi untuk mendapatkan semua produk
function getProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM menu");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk menambahkan produk baru
function addProduct($nama, $kategori, $harga, $status) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO menu (nama, kategori, harga, status) VALUES (:nama, :kategori, :harga, :status)");
    $stmt->execute([
        'nama' => $nama,
        'kategori' => $kategori,
        'harga' => $harga,
        'status' => $status
    ]);
}

// Fungsi untuk mengedit produk
function updateProduct($id, $nama, $kategori, $harga, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE menu SET nama = :nama, kategori = :kategori, harga = :harga, status = :status WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'nama' => $nama,
        'kategori' => $kategori,
        'harga' => $harga,
        'status' => $status
    ]);

    // Mengembalikan true jika baris terpengaruh, false jika tidak
    return $stmt->rowCount() > 0;
}


// Fungsi untuk menghapus produk
function deleteProduct($id) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Tambahkan debugging untuk memastikan hasil eksekusi
        if ($stmt->rowCount() > 0) {
            return true; // Produk berhasil dihapus
        } else {
            return false; // Produk dengan ID tidak ditemukan
        }
    } catch (PDOException $e) {
        // Tangani error database
        error_log("Error saat menghapus produk: " . $e->getMessage());
        throw $e; // Opsional: melempar error untuk debugging
    }
}

?>
