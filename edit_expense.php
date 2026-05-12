<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$message = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: transaction.php");
    exit;
}
$expenseID = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Expenses WHERE expenseID = ? AND userID = ?");
$stmt->execute([$expenseID, $userID]);
$expense = $stmt->fetch();

if (!$expense) {
    die("Record not found or access denied!");
}

$catStmt = $pdo->prepare("SELECT * FROM Categories WHERE userID = ? OR userID is NULL ORDER BY categoryName ASC");
$catStmt->execute([$userID]);
$categories = $catStmt->fetchAll();

function getCategoryIcon($name){
    $name = strtolower($name);
    if (strpos($name, 'food') !== false) return '🍔';
    if (strpos($name, 'transport') !== false) return '🚗';
    if (strpos($name, 'rent') !== false) return '🏚️';
    if (strpos($name, 'shopping') !== false) return '🛍️';
    if (strpos($name, 'daily_use') !== false) return '🧻';
    if (strpos($name, 'entertainment') !== false) return '🎉';
    if (strpos($name, 'travel') !== false) return '✈️';
    if (strpos($name, 'medical') !== false) return '💊';
    if (strpos($name, 'education') !== false) return '🎓';
    if (strpos($name, 'pet') !== false) return '😼';
    if (strpos($name, 'repayment') !== false) return '👛';
    if (strpos($name, 'loan') !== false) return '💸';
    if (strpos($name, 'game') !== false) return '🎮';
    if (strpos($name, 'others') !== false) return '💬';
    return '📦';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $categoryID = $_POST['categoryID'];
    $date = $_POST['expense_date'];
    $desc = trim($_POST['description']);
    $idToUpdate = $_POST['id']; 

    if (!empty($amount) && !empty($categoryID) && !empty($date)) {
        $sql = "UPDATE Expenses SET categoryID=?, amount=?, expense_date=?, description=? WHERE expenseID=? AND userID=?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$categoryID, $amount, $date, $desc, $idToUpdate, $userID])) {
            header("Location: transaction.php"); 
            exit;
        } else {
            $message = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Expense</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="auth-container">
        <div class="register-wrapper orange-wrapper">
            
            <div class="register-left orange-theme">
                
                <div style="position: absolute; top: 20px; left: 30px; margin-top: 10px;">
                    <a href="transaction.php" class="btn-small orange-back" style="padding: 6px 10px;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

                <img src="images/expense.gif" alt="Editing Cat" class="cat-logo" style="width: 200px; border-radius: 20px;">
                
                <h1>FIXING TYPO? 🍊</h1>
                <p style="font-size: 2rem;">User: <strong><?php echo htmlspecialchars($userName); ?></strong></p>
                <p style="font-size: 1.5rem;">Don't worry, mistakes happen.<br>Let's fix it! ✏️</p>
            </div>

            <div class="register-right orange-inputs">
                <h2 class="orange-title">EDIT RECORD 📝</h2>

                <?php if($message): ?><p style="color:red; text-align:center;"><?php echo $message; ?></p><?php endif; ?>

                <form method="POST" id="editForm">
                    <input type="hidden" name="id" value="<?php echo $expense['expenseID']; ?>">

                    <div class="input-group">
                        <div style="display:flex; justify-content:space-between;">
                            <label style="color: #E65100;">AMOUNT (RM)</label>
                            <i class="fas fa-calculator calc-trigger" onclick="toggleKeypad()" style="cursor:pointer; color:#FF9800; font-size:1.4rem;" title="Keypad"></i>
                        </div>
                        <input type="text" inputmode="decimal" name="amount" id="amountInput" 
                               value="<?php echo htmlspecialchars($expense['amount']); ?>" 
                               style="font-size: 1.8rem; font-weight: bold; color: #E65100;" required autocomplete="off">
                    </div>

                    <div class="num-keypad orange-keypad" id="keypad">
                        <button type="button" class="num-btn" onclick="addNum('1')">1</button>
                        <button type="button" class="num-btn" onclick="addNum('2')">2</button>
                        <button type="button" class="num-btn" onclick="addNum('3')">3</button>
                        <button type="button" class="num-btn" onclick="addNum('4')">4</button>
                        <button type="button" class="num-btn" onclick="addNum('5')">5</button>
                        <button type="button" class="num-btn" onclick="addNum('6')">6</button>
                        <button type="button" class="num-btn" onclick="addNum('7')">7</button>
                        <button type="button" class="num-btn" onclick="addNum('8')">8</button>
                        <button type="button" class="num-btn" onclick="addNum('9')">9</button>
                        <button type="button" class="num-btn" onclick="addNum('.')">.</button>
                        <button type="button" class="num-btn" onclick="addNum('0')">0</button>
                        <button type="button" class="num-btn" onclick="clearNum()" style="color:red; border-color:red;">C</button>
                    </div>

                    <div class="input-group">
                        <label style="color: #E65100;">CATEGORY</label>
                        <select name="categoryID" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['categoryID']; ?>" 
                                    <?php echo ($cat['categoryID'] == $expense['categoryID']) ? 'selected' : ''; ?>>
                                    <?php echo getCategoryIcon($cat['categoryName']) . ' ' . htmlspecialchars($cat['categoryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label style="color: #E65100;">DATE</label>
                        <input type="date" name="expense_date" value="<?php echo $expense['expense_date']; ?>" required>
                    </div>

                    <div class="input-group">
                        <label style="color: #E65100;">DESCRIPTION</label>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($expense['description']); ?>" placeholder="Why did you change this?" autocomplete="off">
                    </div>

                    <button type="submit" class="create-btn orange-btn">
                        <i class="fas fa-check"></i> UPDATE CHANGE
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleKeypad() {
            var k = document.getElementById('keypad');
            k.style.display = (k.style.display === 'grid') ? 'none' : 'grid';
        }
        function addNum(n) {
            var input = document.getElementById('amountInput');
            var currentVal = input.value;
            if (n === '.' && currentVal.includes('.')) return;
            if (n === '.' && currentVal === '') { input.value = "0."; return; }
            input.value += n;
        }
        function clearNum() { document.getElementById('amountInput').value = ""; }
    </script>

</body>
</html>