<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$search = isset($_GET['search']) ? $_GET['search'] : '';
$year = date('Y', strtotime($selectedMonth));
$month = date('m', strtotime($selectedMonth));

$sumExpSql = "SELECT SUM(amount) FROM Expenses WHERE userID = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?";
$sumStmt = $pdo->prepare($sumExpSql);
$sumStmt->execute([$userID, $selectedMonth]);
$monthlyTotalExpense = $sumStmt->fetchColumn() ?: 0;

$sumIncSql = "SELECT SUM(amount) FROM Income WHERE userID = ? AND DATE_FORMAT(income_date, '%Y-%m') = ?";
$sumIncStmt = $pdo->prepare($sumIncSql);
$sumIncStmt->execute([$userID, $selectedMonth]);
$monthlyTotalIncome = $sumIncStmt->fetchColumn() ?: 0;

$monthlyBalance = $monthlyTotalIncome - $monthlyTotalExpense;

$dailySql = "SELECT DATE_FORMAT(expense_date, '%d') as day, SUM(amount) as total
             FROM Expenses
             WHERE userID = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ? 
             GROUP BY day";
$dailyStmt = $pdo->prepare($dailySql);
$dailyStmt->execute([$userID, $selectedMonth]);
$dailyData = $dailyStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$dailyIncSql = "SELECT DATE_FORMAT(income_date, '%d') as day, SUM(amount) as total
                FROM Income
                WHERE userID = ? AND DATE_FORMAT(income_date, '%Y-%m') = ?
                GROUP BY day";
$dailyIncStmt = $pdo->prepare($dailyIncSql);
$dailyIncStmt->execute([$userID, $selectedMonth]);
$dailyIncomeData = $dailyIncStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$params = [];

$expQuery = "SELECT expenseID as id, 'expense' as type, amount, expense_date as record_date, description, c.categoryName
             FROM Expenses e
             LEFT JOIN Categories c ON e.categoryID = c.categoryID
             WHERE e.userID = ? AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?";
$params[] = $userID;
$params[] = $selectedMonth;

if (!empty($search)) {
    $expQuery .= " AND (c.categoryName LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$incQuery = "SELECT incomeID as id, 'income' as type, amount, income_date as record_date, description, ic.categoryName 
             FROM Income i 
             LEFT JOIN IncomeCategories ic ON i.incomeCategoryID = ic.incomeCategoryID 
             WHERE i.userID = ? AND DATE_FORMAT(i.income_date, '%Y-%m') = ?";
$params[] = $userID;
$params[] = $selectedMonth;

if (!empty($search)) {
    $incQuery .= " AND (ic.categoryName LIKE ? OR i.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$finalSql = "$expQuery UNION ALL $incQuery ORDER BY record_date DESC";

$listStmt = $pdo->prepare($finalSql);
$listStmt->execute($params);
$transactions = $listStmt->fetchAll(); 

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfWeek = date('w', strtotime("$year-$month-01"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Transactions Record</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="action-bar">
        <a href="dashboard.php" class="btn-small">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <form method="GET" class="date-picker-form">
            <label style="font-weight:bold; margin-right:5px;">YEAR & MONTH:</label>
            <input type="month" name="month" value="<?php echo $selectedMonth; ?>" onchange="this.form.submit()">
        </form>
    </div>

    <h2 style="margin-top: 0; margin-left: 5px; margin-bottom: 15px; color: var(--text-color);"> USER: <?php echo htmlspecialchars($userName); ?> </h2>
    <div class="top-section-grid">
        
        <div class="summary-card">
            <h3 style="margin-bottom: 10px;">MONTHLY SUMMARY</h3>
            <div style="margin-bottom: 15px;">
                <span style="font-size: 1.1rem; color: grey;">Balance</span><br>
                <strong style="font-size: 2rem; color: <?php echo $monthlyBalance >= 0 ? 'var(--text-color)' : 'red'; ?>;">
                    RM <?php echo number_format($monthlyBalance, 2); ?>
                </strong>
            </div>
            <div style="font-size: 1.3rem; text-align: left; width: 80%;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Income:</span><span style="color: green;">+ <?php echo number_format($monthlyTotalIncome, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Expense:</span><span style="color: red;">- <?php echo number_format($monthlyTotalExpense, 2); ?></span>
                </div>
            </div>
        </div>

        <div class="calendar-card">
            <div style="text-align: center; font-size: 1.4rem; margin-bottom: 5px;">📅 CALENDAR VIEW</div>
            <div class="calendar-grid">
                <div class="calendar-header">Sun</div>
                <div class="calendar-header">Mon</div>
                <div class="calendar-header">Tue</div>
                <div class="calendar-header">Wed</div>
                <div class="calendar-header">Thu</div>
                <div class="calendar-header">Fri</div>
                <div class="calendar-header">Sat</div>

                <?php for($i=0; $i<$firstDayOfWeek; $i++): ?>
                    <div class="calendar-day" style="background: transparent; border: none;"></div>
                <?php endfor; ?>

                <?php for($day=1; $day<=$daysInMonth; $day++): ?>
                    <?php 
                        $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $dayExpense = isset($dailyData[$dayKey]) ? $dailyData[$dayKey] : 0;
                        $dayIncome = isset($dailyIncomeData[$dayKey]) ? $dailyIncomeData[$dayKey] : 0;                    ?>
                    <div class="calendar-day">
                        <div class="day-number"><?php echo $day; ?></div>
                        <span class="day-stat day-income">In: <?php echo $dayIncome > 0 ? number_format($dayIncome, 2): '-'; ?></span>
                        <?php if($dayExpense > 0): ?>
                            <span class="day-stat day-expense">Ex: <?php echo number_format($dayExpense, 2); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="table-header-area">
        <h2 style="margin: 0; margin-left: 10px;">TRANSACTIONS RECORD 📝</h2>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <form method="GET" style="display: flex; align-items: center; background: white; border: 2px solid var(--accent-pink); border-radius: 10px; padding: 5px 10px;">
                <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
                <input type="text" name="search" placeholder="Search records..." value="<?php echo htmlspecialchars($search); ?>" 
                       style="border: none; outline: none; font-family: 'VT323'; font-size: 1.2rem; width: 140px; background: transparent;">
                <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--accent-pink);">
                    <i class="fas fa-search"></i>
                </button>
                <?php if(!empty($search)): ?>
                    <a href="transaction.php?month=<?php echo $selectedMonth; ?>" style="margin-left:5px; color:grey;"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>

            <a href="add_income.php" class="create-btn-income">
                <i class="fas fa-wallet"></i> ADD INCOME
            </a>

            <a href="add_expense.php" class="create-btn" style="padding: 10px 20px; font-size: 1.2rem; width: auto; margin:0; white-space: nowrap;">
                <i class="fas fa-minus-circle"></i> ADD EXPENSE
            </a>
        </div>
    </div>

    <table class="cat-table">
        <thead>
            <tr>
                <th>DATE</th>
                <th>CATEGORY</th>
                <th>AMOUNT (RM)</th>
                <th>DESCRIPTION</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
<?php if (count($transactions) > 0): ?>
                <?php foreach ($transactions as $row): ?>
                    <tr style="<?php echo ($row['type'] == 'income') ? 'background-color: #F0FFF0;' : ''; ?>">
                        
                        <td><?php echo htmlspecialchars($row['record_date']); ?></td>
                        
                        <td>
                            <span style="background: white; padding: 5px 10px; border-radius: 10px; font-size: 1.1rem; 
                                border: 1px solid <?php echo ($row['type'] == 'income') ? '#32CD32' : 'pink'; ?>;">
                                <?php echo htmlspecialchars($row['categoryName']); ?>
                            </span>
                        </td>
                        
                        <td style="font-weight: bold; font-size: 1.4rem; color: <?php echo ($row['type'] == 'income') ? '#006400' : '#D81B60'; ?>;">
                            <?php echo ($row['type'] == 'income' ? '+' : '-') . number_format($row['amount'], 2); ?>
                        </td>
                        
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        
                        <td>
                            <?php if ($row['type'] == 'income'): ?>
                                <a href="edit_income.php?id=<?php echo $row['id']; ?>" class="action-icon" style="color: #006400;"><i class="fas fa-edit"></i></a>
                                
                                <a href="javascript:void(0)" onclick="showDeleteModal(<?php echo $row['id']; ?>, 'income')" class="action-icon" style="color: #006400;">
                                    <i class="fas fa-trash-alt"></i>
                                </a>

                            <?php else: ?>
                                <a href="edit_expense.php?id=<?php echo $row['id']; ?>" class="action-icon"><i class="fas fa-edit"></i></a>
                                
                                <a href="javascript:void(0)" onclick="showDeleteModal(<?php echo $row['id']; ?>, 'expense')" class="action-icon">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: grey; padding: 30px;">No transactions found. Start recording! 🐱</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><br>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <img src="images/delete.webp" alt="Shocked Cat" class="modal-cat" style="width: 200px; border-radius: 10px; margin-top: 20px;">
            
            <h2 class="modal-title">DELETE THIS? 🙀</h2>
            <p style="font-size: 1.3rem; color: #555;">Are you sure you want to delete this record?</p>
            <p style="font-size: 1.1rem; color: #999;">(This cannot be undone!)</p>

            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal()">CANCEL</button>
                
                <a href="#" id="confirmDeleteBtn" class="btn-yes">YES, DELETE</a>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal(id, type) {
            var modal = document.getElementById('deleteModal');
            var confirmBtn = document.getElementById('confirmDeleteBtn');

            if (type === 'income') {
                confirmBtn.href = "delete_income.php?id=" + id;
            } else {
                confirmBtn.href = "delete_expense.php?id=" + id;
            }

            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        document.getElementById('deleteModal').onclick = function(e) {
            if (e.target === this) {
                closeModal();
            }
        }
    </script>
</body>
</html>