<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$message = "";

$catStmt = $pdo->prepare("SELECT * FROM Categories 
            WHERE userID IS NULL OR userID = ? 
            ORDER BY 
                CASE WHEN userID IS NULL THEN 0 ELSE 1 END,
                categoryName ASC");
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

    if (!empty($amount) && !empty($categoryID) && !empty($date) && is_numeric($amount) && $amount > 0) {
        $sql = "INSERT INTO Expenses (userID, categoryID, amount, expense_date, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$userID, $categoryID, $amount, $date, $desc])) {
            header("Location: transaction.php");
            exit;
        } else {
            $message = "Save failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Expense</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-container">
        <div class="register-wrapper">
            
            <div class="register-left pink-theme">
                
                <div style="position: absolute; top: 20px; left: 30px; margin-top: 10px;">
                    <a href="transaction.php" class="btn-small" style="padding: 6px 10px; ">
                        <i class="fas fa-arrow-left"></i> Back to Transactions
                    </a>
                </div>

                <img src="images/expense.gif" alt="Expense Cat" class="cat-logo" style="width: 200px; border-radius: 20px;">
                
                <h1>SPENDING AGAIN?</h1>
                <p style="font-size: 2rem;">User: <strong><?php echo htmlspecialchars($userName); ?></strong></p>
                <p style="font-size: 1.5rem;">Record it before you forget! 💸</p>
            </div>

            <div class="register-right">
                <h2 style="color: var(--accent-pink);">RECORD EXPENSE 📝</h2>

                <?php if($message): ?><p style="color:red; text-align:center;"><?php echo $message; ?></p><?php endif; ?>

                <form method="POST" id="expenseForm">
                    
                    <div class="input-group">
                        <div style="display:flex; justify-content:space-between;">
                            <label>AMOUNT (RM)</label>
                            <i class="fas fa-calculator calc-trigger" onclick="toggleKeypad()" style="cursor:pointer; color:var(--accent-pink); font-size:1.4rem;" title="Keypad"></i>
                        </div>
                        <input type="text" inputmode="decimal" name="amount" id="amountInput" placeholder="0.00" style="font-size: 2rem; font-weight: bold; color: #D81B60;" required autocomplete="off">                    </div>

                    <div class="num-keypad" id="keypad">
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
                        <label>CATEGORY</label>
                        <select name="categoryID" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['categoryID']; ?>">
                                    <?php echo getCategoryIcon($cat['categoryName']) . ' ' . htmlspecialchars($cat['categoryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>DATE</label>
                        <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="input-group">
                        <label>DESCRIPTION</label>
                        <input type="text" name="description" placeholder="Short description..." autocomplete="off">
                    </div>

                    <button type="submit" class="create-btn">
                        <i class="fas fa-save"></i> SAVE RECORD
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleKeypad() {
            var k = document.getElementById('keypad');

            if (k.style.display === 'grid') {
                k.style.display = 'none';
            } else {
                k.style.display = 'grid';
            }
        }        
        
        function addNum(n) { 
            var input = document.getElementById('amountInput');
            var currentVal = input.value;

            if (n === '.' && currentVal.includes('.')) {
                return;
            }

            if (n === '.' && currentVal === '') {
                input.value = "0.";
                return;
            }
        input.value += n;
    }        
        function clearNum() { document.getElementById('amountInput').value = ""; }
    </script>

</body>
</html>
