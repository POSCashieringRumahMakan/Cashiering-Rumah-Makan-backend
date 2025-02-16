<?php
class Kasir {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ✅ Get semua transaksi
    public function getAllTransaksi() {
        $stmt = $this->pdo->query("SELECT * FROM transaksi");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Get transaksi berdasarkan ID
    public function getTransaksiById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM transaksi WHERE id_transaksi = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Buat transaksi baru
    public function buatTransaksi($pengguna_id, $table_id, $metode_pembayaran, $menu, $nama_pengguna) {
        $this->pdo->beginTransaction();

        try {
            // Simpan transaksi utama
            $stmt = $this->pdo->prepare("INSERT INTO transaksi (pengguna_id, table_id, metode_pembayaran, nama_pengguna) VALUES (?, ?, ?, ?)");
            $stmt->execute([$pengguna_id, $table_id, $metode_pembayaran, $nama_pengguna]);
            $id_transaksi = $this->pdo->lastInsertId();

            // Simpan setiap item menu dalam transaksi_detail
            foreach ($menu as $item) {
                $stmt = $this->pdo->prepare("INSERT INTO transaksi_detail (id_transaksi, menu_id, kuantitas) VALUES (?, ?, ?)");
                $stmt->execute([$id_transaksi, $item['menu_id'], $item['kuantitas']]);
            }

            $this->pdo->commit();
            return ["id_transaksi" => $id_transaksi];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Gagal menyimpan transaksi: " . $e->getMessage());
        }
    }

    // ✅ Update transaksi (hanya metode pembayaran untuk sekarang)
    public function updateTransaksi($id_transaksi, $metode_pembayaran) {
        $stmt = $this->pdo->prepare("UPDATE transaksi SET metode_pembayaran = ? WHERE id_transaksi = ?");
        $stmt->execute([$metode_pembayaran, $id_transaksi]);

        if ($stmt->rowCount() > 0) {
            return ["id_transaksi" => $id_transaksi, "metode_pembayaran" => $metode_pembayaran];
        } else {
            throw new Exception("Transaksi tidak ditemukan atau tidak ada perubahan");
        }
    }

    // ✅ Hapus transaksi berdasarkan ID
    public function deleteTransaksi($id_transaksi) {
        $stmt = $this->pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
        $stmt->execute([$id_transaksi]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("Transaksi tidak ditemukan");
        }
    }
}
?>
