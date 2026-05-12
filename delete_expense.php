<?php
require_once 'auth.php';
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: transaction.php");
    exit;
}

$expenseID = $_GET['id'];
$userID = $_SESSION['user_id'];

$sql = "DELETE FROM Expenses WHERE expenseID = ? AND userID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$expenseID, $userID]);

header("Location: transaction.php");
exit;
?>