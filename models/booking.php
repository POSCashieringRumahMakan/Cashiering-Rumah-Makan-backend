<?php
class Booking {
    private $conn;

    public function __construct() {
        // Koneksi ke database
        $this->conn = new mysqli("localhost", "root", "", "posrumah_makan");

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    // Ambil semua data booking
    public function getAllBookings() {
        $query = "SELECT * FROM bookings";
        $result = $this->conn->query($query);

        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        return $bookings;
    }

    // Ambil data booking hari ini
    public function getBookingsToday() {
        $today = date("Y-m-d");
        $query = "SELECT * FROM bookings WHERE DATE(booking_time) = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        return $bookings;
    }

    // Ambil data booking besok
    public function getBookingsTomorrow() {
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $query = "SELECT * FROM bookings WHERE DATE(booking_time) = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $tomorrow);
        $stmt->execute();
        $result = $stmt->get_result();

        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        return $bookings;
    }

    // Tambah booking baru
    public function addBooking($table_id, $customer_name, $booking_time) {
        // Cek apakah meja tersedia pada waktu tersebut
        if ($this->checkTableAvailability($table_id, $booking_time)) {
            // Menambahkan booking dengan status langsung "confirmed"
            $status = 'confirmed';
            $stmt = $this->conn->prepare("INSERT INTO bookings (table_id, customer_name, booking_time, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $table_id, $customer_name, $booking_time, $status);
            if ($stmt->execute()) {
                // Update status meja menjadi tidak tersedia setelah booking
                $this->updateTableStatus($table_id, 'unavailable');
                return true;
            }
        }
        return false; // Jika meja tidak tersedia
    }

    // Cek apakah meja tersedia pada waktu tertentu
    public function checkTableAvailability($table_id, $booking_time) {
        $query = "SELECT * FROM bookings WHERE table_id = ? AND booking_time = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $table_id, $booking_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Meja sudah dibooking
        }
        return true; // Meja tersedia
    }

    // Update status meja
    public function updateTableStatus($table_id, $status) {
        $stmt = $this->conn->prepare("UPDATE tables SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $table_id);
        $stmt->execute();
    }

    // Update booking
    public function updateBooking($id, $table_id, $customer_name, $booking_time) {
        $stmt = $this->conn->prepare("UPDATE bookings SET table_id = ?, customer_name = ?, booking_time = ? WHERE id = ?");
        $stmt->bind_param("issi", $table_id, $customer_name, $booking_time, $id);
        return $stmt->execute();
    }

    // Hapus booking
    public function deleteBooking($id) {
        // Ambil table_id untuk update status meja
        $stmt = $this->conn->prepare("SELECT table_id FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $table_id = $result->fetch_assoc()['table_id'];

        // Hapus booking
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Update status meja menjadi tersedia setelah pembatalan booking
            $this->updateTableStatus($table_id, 'available');
            return true;
        }
        return false;
    }
}
