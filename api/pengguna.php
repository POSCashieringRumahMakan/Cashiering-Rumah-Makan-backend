<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // Mengatur header sebagai JSON

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/pengguna.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$pengguna = new Pengguna();

// if ($method === 'POST') {
//     $data = json_decode(file_get_contents("php://input"));

//     if (empty($data->nama) || empty($data->email) || empty($data->noTelepon) || empty($data->tingkatan)) {
//         echo json_encode(['message' => 'Semua field harus diisi!']);
//         http_response_code(400);
//         exit;
//     }

//     if ($pengguna->create($data->nama, $data->email, $data->noTelepon, $data->tingkatan)) {
//         echo json_encode(['message' => 'Pengguna berhasil ditambahkan!']);
//         http_response_code(201);
//     } else {
//         echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
//         http_response_code(500);
//     }
// }

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nama) || empty($data->email) || empty($data->noTelepon) || empty($data->tingkatan) || empty($data->harga) || empty($data->metode_pembayaran)) {
        echo json_encode(['message' => 'Semua field harus diisi!']);
        http_response_code(400);
        exit;
    }

    // Tentukan status pembayaran berdasarkan metode pembayaran
    $status = ($data->metode_pembayaran === 'Dana') ? 'Lunas' : 'Belum Dibayar';

    if ($pengguna->create($data->nama, $data->email, $data->noTelepon, $data->tingkatan, $data->harga, $data->metode_pembayaran, $status)) {
        echo json_encode(['message' => 'Pengguna berhasil ditambahkan!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if ($id) {
        $result = $pengguna->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Pengguna tidak ditemukan.']);
            http_response_code(404);
        }
    } else {
        $result = $pengguna->getAll();
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Tidak ada data pengguna.']);
            http_response_code(404);
        }
    }
}

// if ($method === 'PUT') {
//     if ($id) {
//         $data = json_decode(file_get_contents("php://input"));

//         if (empty($data->nama) || empty($data->email) || empty($data->noTelepon) || empty($data->tingkatan)) {
//             echo json_encode(['message' => 'Semua field harus diisi!']);
//             http_response_code(400);
//             exit;
//         }

//         if ($pengguna->update($id, $data->nama, $data->email, $data->noTelepon, $data->tingkatan)) {
//             echo json_encode(['message' => 'Pengguna berhasil diperbarui!']);
//             http_response_code(200);
//         } else {
//             echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
//             http_response_code(500);
//         }
//     } else {
//         echo json_encode(['message' => 'ID pengguna tidak ditemukan.']);
//         http_response_code(400);
//     }
// }

if ($method === 'PUT') {
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        // Partial update: cek hanya data yang dikirim
        $fields = [];
        if (isset($data->nama)) $fields['nama'] = $data->nama;
        if (isset($data->email)) $fields['email'] = $data->email;
        if (isset($data->noTelepon)) $fields['noTelepon'] = $data->noTelepon;
        if (isset($data->tingkatan)) $fields['tingkatan'] = $data->tingkatan;
        if (isset($data->harga)) $fields['harga'] = $data->harga;
        if (isset($data->metode_pembayaran)) {
            $fields['metode_pembayaran'] = $data->metode_pembayaran;
        
            // Jika metode pembayaran Dana, langsung lunas
            if ($data->metode_pembayaran === 'Dana') {
                $fields['status'] = 'Lunas';
            } 
            // Jika Cash, cek apakah ada konfirmasi pembayaran
            elseif ($data->metode_pembayaran === 'Cash' && isset($data->pembayaran_berhasil) && $data->pembayaran_berhasil) {
                $fields['status'] = 'Lunas';
            }            
            // Jika metode Cash tapi belum dikonfirmasi, tetap "Belum Dibayar"
            else {
                $fields['status'] = 'Belum Dibayar';
            }
        }

        if ($pengguna->partialUpdate($id, $fields)) {
            echo json_encode(['message' => 'Status pembayaran berhasil diperbarui!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Gagal memperbarui status pembayaran.']);
            http_response_code(500);
        }
        

        if ($pengguna->partialUpdate($id, $fields)) {
            echo json_encode(['message' => 'Pengguna berhasil diperbarui!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pengguna tidak ditemukan.']);
        http_response_code(400);
    }
}


if ($method === 'DELETE') {
    if ($id) {
        if ($pengguna->delete($id)) {
            echo json_encode(['message' => 'Pengguna berhasil dihapus!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Terjadi kesalahan, coba lagi.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'ID pengguna tidak ditemukan.']);
        http_response_code(400);
    }
}
?>
