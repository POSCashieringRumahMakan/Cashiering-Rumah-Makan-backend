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

    // Menambah meja baru
    public function addTable($number, $capacity, $status) {
        $stmt = $this->conn->prepare("INSERT INTO tables (number, capacity, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $number, $capacity, $status);
        $stmt->execute();
    }

    // Mengupdate meja
    public function updateTable($id, $number, $capacity, $status) {
        $stmt = $this->conn->prepare("UPDATE tables SET number = ?, capacity = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sisi", $number, $capacity, $status, $id);
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
