<?php
class Table {
    private $conn;

    public function __construct() {
        // Koneksi ke database
        $this->conn = new mysqli("localhost", "root", "", "posrumah_makan");

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    // Ambil semua data meja
    public function getAllTables() {
        $query = "SELECT * FROM tables";
        $result = $this->conn->query($query);

        $tables = [];
        while ($row = $result->fetch_assoc()) {
            $tables[] = $row;
        }
        return $tables;
    }

    // Menambah meja baru dengan lokasi
    public function addTable($number, $capacity, $status, $location) {
        $stmt = $this->conn->prepare("INSERT INTO tables (number, capacity, status, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $number, $capacity, $status, $location);
        $stmt->execute();
    }

    // Mengupdate meja dengan lokasi
    public function updateTable($id, $number, $capacity, $status, $location) {
        $stmt = $this->conn->prepare("UPDATE tables SET number = ?, capacity = ?, status = ?, location = ? WHERE id = ?");
        $stmt->bind_param("sissi", $number, $capacity, $status, $location, $id);
        $stmt->execute();
    }

    // Menghapus meja
    public function deleteTable($id) {
        $stmt = $this->conn->prepare("DELETE FROM tables WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}
?>







