<?php
require_once 'auth.php';
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: transaction.php");
    exit;
}

$incomeID = $_GET['id'];
$userID = $_SESSION['user_id'];

$sql = "DELETE FROM Income WHERE incomeID = ? AND userID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$incomeID, $userID]);

header("Location: transaction.php");
exit;
?>