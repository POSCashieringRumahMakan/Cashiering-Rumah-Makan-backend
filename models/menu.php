<?php
require_once __DIR__ . '/../database/db.php';

// Fungsi untuk mendapatkan semua produk
function getProducts() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT menu.id, menu.nama, menu.harga, menu.status, menu.id_kategori, kategori.nama_kategori 
        FROM menu 
        JOIN kategori ON menu.id_kategori = kategori.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Fungsi untuk menambahkan produk baru
function addProduct($nama, $id_kategori, $harga, $status) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO menu (nama, id_kategori, harga, status) 
        VALUES (:nama, :id_kategori, :harga, :status)
    ");
    $stmt->execute([
        'nama' => $nama,
        'id_kategori' => $id_kategori,
        'harga' => $harga,
        'status' => $status
    ]);
    return $pdo->lastInsertId(); // Mengembalikan ID menu yang baru ditambahkan
}


// Fungsi untuk mengedit produk
function updateProduct($id, $nama, $id_kategori, $harga, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE menu SET nama = :nama, id_kategori = :id_kategori, harga = :harga, status = :status WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'nama' => $nama,
        'id_kategori' => $id_kategori,
        'harga' => $harga,
        'status' => $status
    ]);
    return $stmt->rowCount() > 0; // Mengembalikan true jika ada perubahan
}

// Fungsi untuk menghapus produk
function deleteProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0; // Mengembalikan true jika ada data yang dihapus
}
?>
