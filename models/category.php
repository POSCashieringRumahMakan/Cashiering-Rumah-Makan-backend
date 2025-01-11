<?php
require_once '../database/db.php';

class Category
{
    // Tambah kategori baru
    public function create($jenis_kategori, $nama_kategori)
    {
        global $pdo;

        $query = "INSERT INTO kategori (jenis_kategori, nama_kategori) VALUES (?, ?)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$jenis_kategori, $nama_kategori]);
    }

    // Ambil semua kategori
    public function getAll()
    {
        global $pdo;

        $query = "SELECT * FROM kategori";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil kategori berdasarkan ID
    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM kategori WHERE id_kategori = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update kategori
    public function update($id, $jenis_kategori, $nama_kategori)
    {
        global $pdo;

        $query = "UPDATE kategori SET jenis_kategori = ?, nama_kategori = ?, diperbarui_pada = CURRENT_TIMESTAMP WHERE id_kategori = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$jenis_kategori, $nama_kategori, $id]);
    }

    // Hapus kategori
    public function delete($id)
    {
        global $pdo;

        $query = "DELETE FROM kategori WHERE id_kategori = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
