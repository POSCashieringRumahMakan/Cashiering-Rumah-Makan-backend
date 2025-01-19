<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/transaction_items.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : null;

$transactionItem = new TransactionItem();

if ($method === 'POST') {
    // Menambahkan item transaksi
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->transaction_id) || empty($data->menu_id) || empty($data->quantity) || empty($data->price)) {
        echo json_encode(['message' => 'All fields are required!']);
        http_response_code(400);
        exit;
    }

    if ($transactionItem->create($data->transaction_id, $data->menu_id, $data->quantity, $data->price)) {
        echo json_encode(['message' => 'Transaction item added successfully!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Error occurred, try again.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    // Menampilkan item transaksi berdasarkan transaction_id
    if ($transaction_id) {
        $result = $transactionItem->getAllByTransactionId($transaction_id);
        echo json_encode($result);
        http_response_code(200);
    } elseif ($id) {
        // Menampilkan item transaksi berdasarkan item_id
        $result = $transactionItem->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Transaction item not found.']);
            http_response_code(404);
        }
    } else {
        echo json_encode(['message' => 'Transaction ID or Item ID is required.']);
        http_response_code(400);
    }
}

if ($method === 'PUT') {
    // Mengupdate item transaksi
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->quantity) || empty($data->price)) {
            echo json_encode(['message' => 'Quantity and Price are required!']);
            http_response_code(400);
            exit;
        }

        if ($transactionItem->update($id, $data->quantity, $data->price)) {
            echo json_encode(['message' => 'Transaction item updated successfully!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Error occurred, try again.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'Item ID not found.']);
        http_response_code(400);
    }
}

if ($method === 'DELETE') {
    // Menghapus item transaksi
    if ($id) {
        if ($transactionItem->delete($id)) {
            echo json_encode(['message' => 'Transaction item deleted successfully!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Error occurred, try again.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'Item ID not found.']);
        http_response_code(400);
    }
}
?>
