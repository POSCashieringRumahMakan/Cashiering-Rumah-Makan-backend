<?php
require_once '../database/db.php';

class Absensi
{
    // Check-in (Tambah data absensi)
    public function checkIn($pengguna_id, $tanggal, $jam_masuk)
    {
        global $pdo;

        $query = "INSERT INTO absensi (pengguna_id, tanggal, jam_masuk) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);

        if ($stmt->execute([$pengguna_id, $tanggal, $jam_masuk])) {
            return true;
        } else {
            // Debugging: Ambil error dari PDO
            print_r($stmt->errorInfo());
            return false;
        }
    }

    // Check-out (Update jam keluar & hitung total jam kerja)
    public function checkOut($id, $jam_keluar)
    {
        global $pdo;

        // Ambil jam_masuk untuk menghitung total jam kerja
        $query = "SELECT jam_masuk FROM absensi WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        $jam_masuk = strtotime($row['jam_masuk']);
        $jam_keluar = strtotime($jam_keluar);
        $total_jam = round(($jam_keluar - $jam_masuk) / 3600, 2); // Konversi detik ke jam

        $updateQuery = "UPDATE absensi SET jam_keluar = ?, total_jam = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        return $updateStmt->execute([date('H:i:s', $jam_keluar), $total_jam, $id]);
    }

    // Ambil semua data absensi
    public function getAll()
    {
        global $pdo;

        $query = "SELECT * FROM absensi";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil absensi berdasarkan ID
    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM absensi WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
