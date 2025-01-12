<?php
require_once '../database/db.php';

class Pengguna
{
    // Tambah pengguna baru
    public function create($nama, $email, $noTelepon, $tingkatan)
    {
        global $pdo;
    
        $query = "INSERT INTO pengguna (nama, email, noTelepon, tingkatan) VALUES (:nama, :email, :noTelepon, :tingkatan)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':noTelepon' => $noTelepon,
            ':tingkatan' => $tingkatan
        ]);
    }
    

    // Ambil semua pengguna
    public function getAll()
    {
        global $pdo;

        $query = "SELECT * FROM pengguna";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil pengguna berdasarkan ID
    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM pengguna WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update pengguna
    public function update($id, $nama, $email, $noTelepon, $tingkatan)
{
    global $pdo;

    $query = "UPDATE pengguna SET nama = ?, email = ?, noTelepon = ?, tingkatan = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$nama, $email, $noTelepon, $tingkatan, $id]);    
}


    // Hapus pengguna
    public function delete($id)
    {
        global $pdo;

        $query = "DELETE FROM pengguna WHERE id = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
