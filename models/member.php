<?php
require_once '../database/db.php';

class Member
{
    // Menambahkan member baru (registrasi)
    public function create($nama, $email, $password)
    {
        global $pdo;

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Query untuk insert data ke tabel member
        $query = "INSERT INTO member (nama, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$nama, $email, $hashedPassword]);
    }

    // Cek apakah email sudah terdaftar
    public function checkEmail($email)
    {
        global $pdo;

        $query = "SELECT * FROM member WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Mengembalikan data user jika ditemukan
    }

    // Verifikasi login (email dan password)
    public function login($email, $password)
    {
        global $pdo;

        $query = "SELECT * FROM member WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Mengembalikan data user jika password valid
        }
        return false; // Jika login gagal
    }
}
?>
