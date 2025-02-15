<?php
require_once '../database/db.php';

class TransactionItem
{
    public function create($transaction_id, $menu_id, $quantity, $price)
    {
        global $pdo;

        $query = "INSERT INTO transaction_items (transaction_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$transaction_id, $menu_id, $quantity, $price]);
    }

    public function getAllByTransactionId($transaction_id)
    {
        global $pdo;

        $query = "SELECT * FROM transaction_items WHERE transaction_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$transaction_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        global $pdo;

        $query = "SELECT * FROM transaction_items WHERE item_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($item_id, $quantity, $price)
    {
        global $pdo;

        $query = "UPDATE transaction_items SET quantity = ?, price = ? WHERE item_id = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$quantity, $price, $item_id]);
    }

    public function delete($item_id)
    {
        global $pdo;

        $query = "DELETE FROM transaction_items WHERE item_id = ?";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([$item_id]);
    }
}
?>
