<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$message = "";

$catStmt = $pdo->query("SELECT * FROM IncomeCategories ORDER BY categoryName ASC");
$categories = $catStmt->fetchAll();

function getIncomeIcon($name){
    $name = strtolower($name);
    if (strpos($name, 'salary') !== false) return '💰'; 
    if (strpos($name, 'rent') !== false) return '🏠';   
    if (strpos($name, 'gift') !== false) return '🎁';   
    if (strpos($name, 'bonus') !== false) return '🌟';  
    if (strpos($name, 'investment') !== false) return '📈'; 
    if (strpos($name, 'borrow') !== false) return '🤝'; 
    if (strpos($name, 'pocket') !== false) return '🧧'; 
    if (strpos($name, 'receiving') !== false) return '💳'; 
    return '📦'; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $categoryID = $_POST['incomeCategoryID']; 
    $date = $_POST['income_date'];
    $desc = trim($_POST['description']);

    if (!empty($amount) && !empty($categoryID) && !empty($date) && is_numeric($amount) && $amount > 0) {
        $sql = "INSERT INTO Income (userID, incomeCategoryID, amount, income_date, description) VALUES (?, ?, ?, ?, ?)";
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
    <title>Record Income</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="auth-container">
        <div class="register-wrapper green-wrapper">
            
            <div class="register-left green-theme">
                
                <div style="position: absolute; top: 20px; left: 30px; margin-top: 10px;">
                    <a href="transaction.php" class="btn-small green-back" style="padding: 6px 10px;">
                        <i class="fas fa-arrow-left"></i> Back to Transactions
                    </a>
            </div>

                <img src="images/income.webp" alt="Happy Rich Cat" class="cat-logo" style="width: 200px; border-radius: 20px;">
                
                <h1>MONEY IN! 🤑</h1>
                <p style="font-size: 2rem;">User: <strong><?php echo htmlspecialchars($userName); ?></strong></p>
                <p style="font-size: 1.5rem;">Rich day! Keep it coming! 💰</p>
            </div>

            <div class="register-right green-inputs">
                <h2 class="green-title">RECORD INCOME 💵</h2>

                <?php if($message): ?><p style="color:red; text-align:center;"><?php echo $message; ?></p><?php endif; ?>

                <form method="POST" id="incomeForm">
                    
                    <div class="input-group">
                        <div style="display:flex; justify-content:space-between;">
                            <label style="color: #006400;">AMOUNT (RM)</label>
                            <i class="fas fa-calculator calc-trigger" onclick="toggleKeypad()" style="cursor:pointer; color:#32CD32; font-size:1.4rem;" title="Keypad"></i>
                        </div>
                        <input type="text" inputmode="decimal" name="amount" id="amountInput" placeholder="0.00" style="font-size: 1.8rem; font-weight: bold; color: #006400;" required autocomplete="off">
                    </div>

                    <div class="num-keypad green-keypad" id="keypad">
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
                        <label style="color: #006400;">SOURCE</label>
                        <select name="incomeCategoryID" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['incomeCategoryID']; ?>">
                                    <?php echo getIncomeIcon($cat['categoryName']) . ' ' . htmlspecialchars($cat['categoryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label style="color: #006400;">DATE</label>
                        <input type="date" name="income_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="input-group">
                        <label style="color: #006400;">DESCRIPTION</label>
                        <input type="text" name="description" placeholder="Where did this money come from?" autocomplete="off">
                    </div>

                    <button type="submit" class="create-btn green-btn">
                        <i class="fas fa-save"></i> SAVE INCOME
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

        function clearNum() {
            document.getElementById('amountInput').value = "";
        }
    </script>

</body>
</html>