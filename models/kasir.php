<?php
class Kasir {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ambil semua pesanan
    public function getAllOrders() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tambah pesanan baru
    public function addOrder($data) {
        $query = "INSERT INTO " . $this->table_name . " (nama, total, metode_pembayaran, status) VALUES (:nama, :total, :metode_pembayaran, :status)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nama", $data['nama']);
        $stmt->bindParam(":total", $data['total']);
        $stmt->bindParam(":metode_pembayaran", $data['metode_pembayaran']);
        $stmt->bindParam(":status", $data['status']);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update pesanan berdasarkan ID
    public function updateOrder($data) {
        $query = "UPDATE " . $this->table_name . " SET nama = :nama, total = :total, metode_pembayaran = :metode_pembayaran, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $data['id']);
        $stmt->bindParam(":nama", $data['nama']);
        $stmt->bindParam(":total", $data['total']);
        $stmt->bindParam(":metode_pembayaran", $data['metode_pembayaran']);
        $stmt->bindParam(":status", $data['status']);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Hapus pesanan berdasarkan ID
    public function deleteOrder($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
