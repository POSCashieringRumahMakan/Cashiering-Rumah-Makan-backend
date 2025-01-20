<?php
class Riwayat {
    private $conn;
    private $table = "riwayat";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua riwayat
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Menambahkan data riwayat baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (customer_name, menu_name, quantity, total_price, table_number) 
                  VALUES (:customer_name, :menu_name, :quantity, :total_price, :table_number)";
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':customer_name', $data['customer_name']);
        $stmt->bindParam(':menu_name', $data['menu_name']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':total_price', $data['total_price']);
        $stmt->bindParam(':table_number', $data['table_number']);

        return $stmt->execute();
    }

    // Mengupdate data riwayat
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET customer_name = :customer_name, menu_name = :menu_name, 
                      quantity = :quantity, total_price = :total_price, table_number = :table_number 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':customer_name', $data['customer_name']);
        $stmt->bindParam(':menu_name', $data['menu_name']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':total_price', $data['total_price']);
        $stmt->bindParam(':table_number', $data['table_number']);

        return $stmt->execute();
    }

    // Menghapus data riwayat
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
?>
