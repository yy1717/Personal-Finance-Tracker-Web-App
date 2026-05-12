<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$lastMonth = date('Y-m', strtotime($selectedMonth . ' -1 month'));

function getMonthTotal($pdo, $uid, $month, $table, $dateCol) {
    $sql = "SELECT SUM(amount) FROM $table WHERE userID = ? AND DATE_FORMAT($dateCol, '%Y-%m') = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$uid, $month]);
    return $stmt->fetchColumn() ?: 0;
}

$currIncome = getMonthTotal($pdo, $userID, $selectedMonth, 'Income', 'income_date');
$currExpense = getMonthTotal($pdo, $userID, $selectedMonth, 'Expenses', 'expense_date');
$lastIncome = getMonthTotal($pdo, $userID, $lastMonth, 'Income', 'income_date');
$lastExpense = getMonthTotal($pdo, $userID, $lastMonth, 'Expenses', 'expense_date');

$incDiff = $currIncome - $lastIncome;
$expDiff = $currExpense - $lastExpense;

$balance = $currIncome - $currExpense;
$savingsRate = ($currIncome > 0) ? ($balance / $currIncome) * 100 : 0;

$budStmt = $pdo->prepare("SELECT amount FROM Budgets WHERE userID = ? AND budget_month = ?");
$budStmt->execute([$userID, $selectedMonth . '-01']);
$budgetAmount = $budStmt->fetchColumn() ?: 0;

$catSql = "SELECT c.categoryName, SUM(e.amount) as total, 
           (SUM(e.amount) / ? * 100) as percentage
           FROM Expenses e
           JOIN Categories c ON e.categoryID = c.categoryID
           WHERE e.userID = ? AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?
           GROUP BY e.categoryID
           ORDER BY total DESC";
$catStmt = $pdo->prepare($catSql);
$catStmt->execute([$currExpense > 0 ? $currExpense : 1, $userID, $selectedMonth]);
$breakdown = $catStmt->fetchAll();

$topSql = "SELECT description, amount, expense_date, c.categoryName 
           FROM Expenses e
           LEFT JOIN Categories c ON e.categoryID = c.categoryID
           WHERE e.userID = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?
           ORDER BY amount DESC LIMIT 3";
$topStmt = $pdo->prepare($topSql);
$topStmt->execute([$userID, $selectedMonth]);
$topItems = $topStmt->fetchAll();

function getCategoryIcon($name){
    $name = strtolower($name);
    $icons = [
        'salary'=>'💰', 'bonus'=>'🌟', 'rent'=>'🏠', 'investment'=>'📈', 'gift'=>'🎁', 
        'borrow'=>'🤝', 'pocket'=>'🧧', 'receiving'=>'💳', 'refund'=>'↩️', 
        'food'=>'🍔', 'drink'=>'🥤', 'transport'=>'🚗', 'fuel'=>'⛽', 'shopping'=>'🛍️', 
        'groceries'=>'🥦', 'entertainment'=>'🎉', 'game'=>'🎮', 'movie'=>'🎬', 
        'travel'=>'✈️', 'hotel'=>'🏨', 'medical'=>'💊', 'doctor'=>'🩺', 'education'=>'🎓', 
        'book'=>'📚', 'pet'=>'😼', 'bill'=>'🧾', 'loan'=>'💸', 'repayment'=>'👛', 'daily'=>'🧻', 'others'=>'💬'
    ];
    foreach ($icons as $keyword => $icon) {
        if (strpos($name, $keyword) !== false) return $icon;
    }
    return '📦'; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Spending Report</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="report-page">

    <div class="action-bar">
        <div style="display: flex; flex-direction: column; margin-top: -6px;">
            <a href="dashboard.php" class="btn-small">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <span style="font-size: 1.2rem; margin-top: 5px; margin-left: 5px; color: var(--text-color);">
                <h2 style="margin-top: 10px; margin-left: 5px; margin-bottom: -5px; color: var(--text-color);"> USER: <?php echo htmlspecialchars($userName); ?> </h2>
            </span>
        </div>
        
        <div class="report-header-right">
            <h1 style="margin: 0; font-size: 3rem; color: var(--text-color); text-align: right; line-height: 1;">
                MONTHLY SPENDING REPORT 📊
            </h1>
            <form method="GET" style="text-align: right; margin-top: 5px;">
                <input type="month" name="month" class="report-month-picker" 
                       value="<?php echo $selectedMonth; ?>" 
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="report-container">

        <div class="overview-grid">
            <div class="overview-card">
                <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="card-label">TOTAL SPEND</div>
                <div class="card-amount">RM <?php echo number_format($currExpense, 2); ?></div>
                <div class="comparison-tag">
                    <?php if($expDiff > 0): ?>
                        <span style="color: #D32F2F;">⬆ RM <?php echo number_format(abs($expDiff), 0); ?> vs last month</span>
                    <?php elseif($expDiff < 0): ?>
                        <span style="color: #388E3C;">⬇ RM <?php echo number_format(abs($expDiff), 0); ?> vs last month</span>
                    <?php else: ?>
                        <span style="color: grey;">- Same as last month</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="overview-card">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <div class="card-label">TOTAL INCOME</div>
                <div class="card-amount">RM <?php echo number_format($currIncome, 2); ?></div>
                <div class="comparison-tag">
                    <?php if($incDiff > 0): ?>
                        <span style="color: #388E3C;">⬆ RM <?php echo number_format(abs($incDiff), 0); ?> vs last month</span>
                    <?php elseif($incDiff < 0): ?>
                        <span style="color: #D32F2F;">⬇ RM <?php echo number_format(abs($incDiff), 0); ?> vs last month</span>
                    <?php else: ?>
                        <span style="color: grey;">- Same as last month</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="overview-card">
                <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
                <div class="card-label">SAVINGS RATE</div>
                <div class="card-amount" style="color: <?php echo $savingsRate > 20 ? '#388E3C' : '#E64A19'; ?>;">
                    <?php echo number_format($savingsRate, 1); ?>%
                </div>
                <div class="comparison-tag">
                    <?php echo ($savingsRate > 20) ? 'Great job! 👍' : 'Keep trying! 💪'; ?>
                </div>
            </div>
        </div>

        <div class="main-content-grid">
            <div class="breakdown-panel">
                <h2 class="panel-title"><i class="fas fa-chart-pie"></i> SPENDING BREAKDOWN</h2>
                <?php if(count($breakdown) > 0): ?>
                    <?php foreach($breakdown as $row): ?>
                        <div class="breakdown-item">
                            <div class="bd-info">
                                <span><?php echo getCategoryIcon($row['categoryName']) . ' ' . htmlspecialchars($row['categoryName']); ?></span>
                                <strong>RM <?php echo number_format($row['total'], 2); ?></strong>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?php echo $row['percentage']; ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; color:grey; padding: 20px;">No expenses yet. 🌸</p>
                <?php endif; ?>
            </div>

            <div class="sidebar-panel">
                <div class="budget-goal-card">
                    <h3>🎯 BUDGET GOAL</h3>
                    <div class="goal-amount">RM <?php echo number_format($budgetAmount, 0); ?></div>
                    <div class="goal-status">
                        <?php 
                            $percentUsed = 0;
                            if($budgetAmount > 0) {
                                $percentUsed = ($currExpense / $budgetAmount) * 100;
                                echo "Used: " . number_format($percentUsed, 1) . "%";
                            } else {
                                echo "No budget set";
                            }
                        ?>
                    </div>
                    <div style="height: 10px; background: rgba(255,255,255,0.3); border-radius: 5px; margin-top: 10px; overflow:hidden;">
                        <div style="height: 100%; width: <?php echo min($percentUsed, 100); ?>%; background: white;"></div>
                    </div>
                </div>

                <div class="top-spenders-box">
                    <h3>🏆 TOP SPENDERS</h3>
                    <?php if(count($topItems) > 0): ?>
                        <?php $rank=1; foreach($topItems as $item): ?>
                            <div class="top-row">
                                <div class="top-rank"><?php echo $rank++; ?></div>
                                <div class="top-desc">
                                    <div class="t-name"><?php echo htmlspecialchars($item['description'] ?: $item['categoryName']); ?></div>
                                    <div class="t-date"><?php echo $item['expense_date']; ?></div>
                                </div>
                                <div class="top-amt">RM <?php echo number_format($item['amount'], 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:grey; text-align: center;">No data yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> 

</body>
</html>