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
    public function checkOut($pengguna_id, $jam_keluar)
    {
        global $pdo;

        // Ambil ID absensi terakhir untuk user yang belum check-out
        $query = "SELECT id, jam_masuk FROM absensi WHERE pengguna_id = ? AND jam_keluar IS NULL ORDER BY id DESC LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pengguna_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false; // Tidak ada absensi yang bisa di-update
        }

        $id = $row['id'];
        $jam_masuk = strtotime($row['jam_masuk']);
        $jam_keluar = strtotime($jam_keluar);
        $total_jam = round(($jam_keluar - $jam_masuk) / 3600, 2);

        // Update jam keluar dan total jam
        $updateQuery = "UPDATE absensi SET jam_keluar = ?, total_jam = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        return $updateStmt->execute([date('H:i:s', $jam_keluar), $total_jam, $id]);
    }

    // Ambil semua data absensi
    public function getAllByUser($pengguna_id)
    {
        global $pdo;

        $query = "SELECT * FROM absensi WHERE pengguna_id = ? ORDER BY tanggal DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pengguna_id]);
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

    public function getAll()
{
    global $pdo;
    $query = "SELECT * FROM absensi ORDER BY tanggal DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
