<?php
require_once '../database/db.php';

class Pegawai
{
    // Tambah pegawai baru
    public function create($nama, $jabatan, $email, $no_telepon, $status)
    {
        global $pdo;

        $query = "INSERT INTO pegawai (nama, jabatan, email, no_telepon, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$nama, $jabatan, $email, $no_telepon, $status]);
    }

    // Ambil semua pegawai
    public function getAll()
    {
        global $pdo;

        $query = "SELECT * FROM pegawai";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil pegawai berdasarkan ID
    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM pegawai WHERE id_pegawai = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update pegawai
    public function update($id, $nama, $jabatan, $email, $no_telepon, $status)
    {
        global $pdo;

        $query = "UPDATE pegawai SET nama = ?, jabatan = ?, email = ?, no_telepon = ?, status = ?, diperbarui_pada = CURRENT_TIMESTAMP WHERE id_pegawai = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$nama, $jabatan, $email, $no_telepon, $status, $id]);
    }

    // Hapus pegawai
    public function delete($id)
    {
        global $pdo;

        $query = "DELETE FROM pegawai WHERE id_pegawai = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
