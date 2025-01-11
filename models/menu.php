<?php
include 'db.php';

// Fungsi untuk mendapatkan semua produk
default:
function getProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produk");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk menambahkan produk baru
function addProduct($nama, $kategori, $harga, $status) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO produk (nama, kategori, harga, status) VALUES (:nama, :kategori, :harga, :status)");
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
    $stmt = $pdo->prepare("UPDATE produk SET nama = :nama, kategori = :kategori, harga = :harga, status = :status WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'nama' => $nama,
        'kategori' => $kategori,
        'harga' => $harga,
        'status' => $status
    ]);
}

// Fungsi untuk menghapus produk
function deleteProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Tangani permintaan berdasarkan metode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            addProduct($_POST['nama'], $_POST['kategori'], $_POST['harga'], $_POST['status']);
            break;
        case 'edit':
            updateProduct($_POST['id'], $_POST['nama'], $_POST['kategori'], $_POST['harga'], $_POST['status']);
            break;
        case 'delete':
            deleteProduct($_POST['id']);
            break;
    }

    // Redirect kembali ke menu.html setelah operasi
    header('Location: menu.html');
    exit;
}

// Jika metode GET, tampilkan data produk dalam format JSON
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = getProducts();
    header('Content-Type: application/json');
    echo json_encode($products);
}
?>
