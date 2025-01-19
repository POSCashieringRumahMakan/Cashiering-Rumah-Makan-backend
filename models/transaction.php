<?php
require_once '../database/db.php';

class Transaction
{
    public function create($customer_name, $total_amount, $payment_method, $status)
    {
        global $pdo;

        $query = "INSERT INTO transactions (customer_name, total_amount, payment_method, status) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$customer_name, $total_amount, $payment_method, $status]);
    }

    public function getAll()
    {
        global $pdo;

        $query = "SELECT * FROM transactions";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM transactions WHERE transaction_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $customer_name, $total_amount, $payment_method, $status)
    {
        global $pdo;

        $query = "UPDATE transactions SET customer_name = ?, total_amount = ?, payment_method = ?, status = ? WHERE transaction_id = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$customer_name, $total_amount, $payment_method, $status, $id]);
    }

    public function delete($id)
    {
        global $pdo;

        $query = "DELETE FROM transactions WHERE transaction_id = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
