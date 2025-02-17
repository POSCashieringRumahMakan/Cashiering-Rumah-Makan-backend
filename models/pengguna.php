<?php
require_once '../database/db.php';

class Pengguna
{
    // Tambah pengguna baru
    // public function create($nama, $email, $noTelepon, $tingkatan)
    // {
    //     global $pdo;
    
    //     $query = "INSERT INTO pengguna (nama, email, noTelepon, tingkatan) VALUES (:nama, :email, :noTelepon, :tingkatan)";
    //     $stmt = $pdo->prepare($query);
    //     return $stmt->execute([
    //         ':nama' => $nama,
    //         ':email' => $email,
    //         ':noTelepon' => $noTelepon,
    //         ':tingkatan' => $tingkatan
    //     ]);
    // }
    
    // Tambah pengguna baru dengan harga & metode pembayaran
    public function create($nama, $email, $noTelepon, $tingkatan, $harga, $metodePembayaran, $status)
    {
        global $pdo;
    
        $query = "INSERT INTO pengguna (nama, email, noTelepon, tingkatan, harga, metode_pembayaran, status) 
                  VALUES (:nama, :email, :noTelepon, :tingkatan, :harga, :metodePembayaran, :status)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':noTelepon' => $noTelepon,
            ':tingkatan' => $tingkatan,
            ':harga' => $harga,
            ':metodePembayaran' => $metodePembayaran,
            ':status' => $status
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
//     public function update($id, $nama, $email, $noTelepon, $tingkatan)
// {
//     global $pdo;

//     $query = "UPDATE pengguna SET nama = ?, email = ?, noTelepon = ?, tingkatan = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?";
//     $stmt = $pdo->prepare($query);
//     return $stmt->execute([$nama, $email, $noTelepon, $tingkatan, $id]);    
// }

// Update pengguna
public function update($id, $nama, $email, $noTelepon, $tingkatan, $harga, $metodePembayaran, $status)
{
    global $pdo;

    $query = "UPDATE pengguna 
              SET nama = ?, email = ?, noTelepon = ?, tingkatan = ?, harga = ?, metode_pembayaran = ?, status = ?, created_at = CURRENT_TIMESTAMP 
              WHERE id = ?";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$nama, $email, $noTelepon, $tingkatan, $harga, $metodePembayaran, $status, $id]);    
}

// public function partialUpdate($id, $fields)
// {
//     global $pdo;

//     $setClause = [];
//     $params = [];

//     foreach ($fields as $key => $value) {
//         $setClause[] = "$key = :$key";
//         $params[":$key"] = $value;
//     }

//     if (empty($setClause)) {
//         return false;
//     }

//     $params[":id"] = $id;
//     $query = "UPDATE pengguna SET " . implode(", ", $setClause) . " WHERE id = :id";
//     $stmt = $pdo->prepare($query);

//     if ($stmt->execute($params)) {
//         return true;
//     } else {
//         // Tampilkan error jika gagal
//         $errorInfo = $stmt->errorInfo();
//         error_log("Gagal update: " . $errorInfo[2]);
//         return false;
//     }
// }


public function partialUpdate($id, $fields)
{
    global $pdo;

    $setClause = [];
    $params = [];

    foreach ($fields as $key => $value) {
        $setClause[] = "$key = :$key";
        $params[":$key"] = $value;
    }

    if (empty($setClause)) {
        return false;
    }

    $params[":id"] = $id;
    $query = "UPDATE pengguna SET " . implode(", ", $setClause) . " WHERE id = :id";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute($params)) {
        error_log("Update berhasil: " . json_encode($fields)); // Tambahkan log
        return true;
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Gagal update: " . $errorInfo[2]); // Tambahkan log error
        return false;
    }
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
