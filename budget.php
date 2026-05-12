<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$message = "";

$selectedMonth = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('Y-m');
$year = date('Y', strtotime($selectedMonth));
$month = date('m', strtotime($selectedMonth));
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $monthToSet = $_POST['month'] . '-01';

    if ($amount !== ""){
        $sql = "INSERT INTO Budgets (userID, budget_month, amount) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE amount = VALUES(amount)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$userID, $monthToSet, $amount])) {
            $message = "Budget Saved! 💰";
            $redirectMonth = $_POST['month'];
            header("Location: budget.php?month=$redirectMonth");
            exit;
        } else {
            $message = "Save failed!";
        }
    }
}

$budStmt = $pdo->prepare("SELECT amount FROM Budgets WHERE userID = ? AND budget_month = ?");
$budStmt->execute([$userID, $selectedMonth . '-01']);
$budgetAmount = $budStmt->fetchColumn() ?: 0;

$expStmt = $pdo->prepare("SELECT SUM(amount) FROM Expenses WHERE userID = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
$expStmt->execute([$userID, $selectedMonth]);
$totalSpent = $expStmt->fetchColumn() ?: 0;

$remaining = $budgetAmount - $totalSpent;

$dailyAverage = ($budgetAmount > 0) ? ($remaining / $daysInMonth) : 0;

$percent = ($budgetAmount > 0) ? ($totalSpent / $budgetAmount) * 100 : 0;
if ($percent > 100) $percent = 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Management</title>
<link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="action-bar" style="margin-bottom: 5px; margin-top: 5px;">
        <div style="display: flex; flex-direction: column;">
            <a href="dashboard.php" class="btn-small">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <span style="font-size: 1.2rem; margin-top: 5px; margin-left: 5px; color: var(--text-color);">
                <h2 style="margin-top: 10px; margin-left: 5px; margin-bottom: -5px; color: var(--text-color);"> USER: <?php echo htmlspecialchars($userName); ?> </h2>
            </span>
        </div>

        <h1 style="margin: 0; color: var(--text-color); font-size: 2.5rem; text-align: right;">
            BUDGET MANAGEMENT 📉
        </h1>
    </div>

    <div class="budget-container">

        <div class="budget-status-card">
            <h1 style="margin-top:0;">THIS MONTH'S BUDGET</h1>
            <p style="font-size: 1.5rem; color: grey; margin-bottom: -10px;">Remaining</p>
            
            <div class="big-money" style="color: <?php echo $remaining >= 0 ? '#32CD32' : 'red'; ?>;">
                RM <?php echo number_format($remaining, 2); ?>
            </div>

            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?php echo $percent; ?>%; background-color: <?php echo $remaining >= 0 ? 'var(--accent-pink)' : 'red'; ?>;"></div>
            </div>

            <div style="width: 100%; text-align: left; padding: 0 20px; margin-top: 10px;">
                <div style="display:flex; justify-content: space-between; font-size: 1.8rem; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">
                    <span>Total Budget:</span>
                    <strong>RM <?php echo number_format($budgetAmount, 2); ?></strong>
                </div>
                <div style="display:flex; justify-content: space-between; font-size: 1.8rem; color: #D81B60; margin-top: 10px;">
                    <span>Total Spent:</span>
                    <strong>- RM <?php echo number_format($totalSpent, 2); ?></strong>
                </div>
            </div>
        </div>

        <div class="budget-form-card">
            <h2 style="color: var(--text-color); margin-top: 0;">SET BUDGET MONTHLY 🎯</h2>

            <?php if($message): ?><p style="color:green; text-align:center; background:white; padding:5px; border-radius:5px;"><?php echo $message; ?></p><?php endif; ?>

            <form method="POST" id="budgetForm">
                
                <div class="input-group">
                    <label>YEAR & MONTH</label>
                    <input type="month" name="month" id="monthPicker" value="<?php echo $selectedMonth; ?>" 
                           onchange="window.location.href='budget.php?month='+this.value">
                </div>

                <div class="input-group">
                    <div style="display:flex; justify-content:space-between;">
                        <label>AMOUNT (RM)</label>
                        <i class="fas fa-calculator calc-trigger" onclick="toggleKeypad()" style="cursor:pointer; color:var(--accent-pink); font-size:1.4rem;"></i>
                    </div>
                    <input type="text" inputmode="decimal" name="amount" id="amountInput" 
                           value="<?php echo ($budgetAmount > 0) ? $budgetAmount : ''; ?>" 
                           placeholder="0.00" style="font-size: 1.8rem; font-weight: bold; color: var(--text-color);" autocomplete="off">
                </div>

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

                <div class="daily-avail">
                    <i class="fas fa-info-circle" style="font-size: 1.7rem;"></i>
                    <span style="font-size: 1.7rem; font-weight: bold; color: #F26B8A;">Remaining daily avg availability:</span><br>
                    <strong style="font-size: 2.0rem; color: #32CD32;">
                        RM <?php echo number_format($dailyAverage, 2); ?> / day
                    </strong>
                    <div style="font-size: 1.2rem; color: grey;">(Based on <?php echo $daysInMonth; ?> days)</div>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="create-btn" style="width: auto; padding: 15px; font-size: 2rem;">
                        <i class="fas fa-save"></i> SAVE BUDGET
                    </button>
                </div>

            </form>
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