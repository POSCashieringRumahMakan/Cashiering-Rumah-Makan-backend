<?php
class Kategori {
    private $conn;
    private $table_name = "kategori";

    public $id_kategori;
    public $jenis_kategori;
    public $nama_kategori;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY dibuat_pada DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (jenis_kategori, nama_kategori) VALUES (:jenis_kategori, :nama_kategori)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':jenis_kategori', $this->jenis_kategori);
        $stmt->bindParam(':nama_kategori', $this->nama_kategori);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET jenis_kategori = :jenis_kategori, nama_kategori = :nama_kategori WHERE id_kategori = :id_kategori";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_kategori', $this->id_kategori);
        $stmt->bindParam(':jenis_kategori', $this->jenis_kategori);
        $stmt->bindParam(':nama_kategori', $this->nama_kategori);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_kategori = :id_kategori";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_kategori', $this->id_kategori);

        return $stmt->execute();
    }
}
