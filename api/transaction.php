<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../models/Transaction.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$transaction = new Transaction();

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->customer_name) || empty($data->total_amount)) {
        echo json_encode(['message' => 'Customer name and total amount are required!']);
        http_response_code(400);
        exit;
    }

    if ($transaction->create($data->customer_name, $data->total_amount, $data->payment_method, $data->status)) {
        echo json_encode(['message' => 'Transaction added successfully!']);
        http_response_code(201);
    } else {
        echo json_encode(['message' => 'Error occurred, try again.']);
        http_response_code(500);
    }
}

if ($method === 'GET') {
    if ($id) {
        $result = $transaction->getById($id);
        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Transaction not found.']);
            http_response_code(404);
        }
    } else {
        $result = $transaction->getAll();
        echo json_encode($result);
        http_response_code(200);
    }
}

if ($method === 'PUT') {
    if ($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->customer_name) || empty($data->total_amount)) {
            echo json_encode(['message' => 'All fields must be filled!']);
            http_response_code(400);
            exit;
        }

        if ($transaction->update($id, $data->customer_name, $data->total_amount, $data->payment_method, $data->status)) {
            echo json_encode(['message' => 'Transaction updated successfully!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Error occurred, try again.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'Transaction ID not found.']);
        http_response_code(400);
    }
}

if ($method === 'DELETE') {
    if ($id) {
        if ($transaction->delete($id)) {
            echo json_encode(['message' => 'Transaction deleted successfully!']);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'Error occurred, try again.']);
            http_response_code(500);
        }
    } else {
        echo json_encode(['message' => 'Transaction ID not found.']);
        http_response_code(400);
    }
}
?>

