<?php
require_once '../models/Booking.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$booking = new Booking();

switch ($method) {
    case 'GET':
        // Menampilkan semua booking atau berdasarkan filter
        if (isset($_GET['filter'])) {
            $filter = $_GET['filter'];

            if ($filter == 'today') {
                header('Content-Type: application/json');
                echo json_encode($booking->getBookingsToday());
            } elseif ($filter == 'tomorrow') {
                header('Content-Type: application/json');
                echo json_encode($booking->getBookingsTomorrow());
            } else {
                header('Content-Type: application/json');
                echo json_encode($booking->getAllBookings());
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode($booking->getAllBookings());
        }
        break;

    case 'POST':
        // Menambah booking baru
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->table_id) && !empty($data->customer_name) && !empty($data->booking_time)) {
            if ($booking->addBooking($data->table_id, $data->customer_name, $data->booking_time)) {
                echo json_encode(["message" => "Booking berhasil ditambahkan dan terkonfirmasi"]);
            } else {
                echo json_encode(["message" => "Meja sudah dibooking pada waktu tersebut"]);
            }
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'PUT':
        // Mengupdate booking
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->table_id) && !empty($data->customer_name) && !empty($data->booking_time)) {
            $booking->updateBooking($data->id, $data->table_id, $data->customer_name, $data->booking_time);
            echo json_encode(["message" => "Booking berhasil diperbarui"]);
        } else {
            echo json_encode(["message" => "Data tidak lengkap"]);
        }
        break;

    case 'DELETE':
        // Menghapus booking
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            if ($booking->deleteBooking($data->id)) {
                echo json_encode(["message" => "Booking berhasil dihapus"]);
            } else {
                echo json_encode(["message" => "Booking gagal dihapus"]);
            }
        } else {
            echo json_encode(["message" => "ID booking tidak ditemukan"]);
        }
        break;
}
