<?php
require_once 'auth.php';
require_once 'db.php';

$userID = $_SESSION['user_id'];
$currentMonth = date('Y-m');

$expenseStmt = $pdo->prepare(
    "SELECT SUM(amount)
     FROM Expenses
     WHERE userID = ?
    AND DATE_FORMAT(expense_date, '%Y-%m') = ?"
);
$expenseStmt->execute([$userID, $currentMonth]);
$totalSpent = $expenseStmt->fetchColumn() ?: 0;

$incomeStmt = $pdo->prepare(
    "SELECT SUM(amount) FROM Income WHERE userID = ? AND DATE_FORMAT(income_date, '%Y-%m') = ?"
);
$incomeStmt->execute([$userID, $currentMonth]);
$totalIncome = $incomeStmt->fetchColumn() ?: 0;

$savings = $totalIncome - $totalSpent;

$budgetStmt = $pdo->prepare(
    "SELECT amount
     FROM Budgets
     WHERE userID = ?
   AND budget_month = ?"
);
$budgetStmt->execute([$userID, $currentMonth . '-01']);
$budget = $budgetStmt->fetchColumn() ?: 0;
$remaining = $budget - $totalSpent;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Finance - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="top-nav">
        <div><i class="fas fa-paw"></i> DASHBOARD</div>
        <div><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d'); ?></div>
    </div>

    <div class="main-layout">
        <div class="sidebar">
            <a href="dashboard.php" class="menu-card"><i class="fas fa-home"></i><br>Dashboard</a>
            <a href="transaction.php" class="menu-card"><i class="fas fa-exchange-alt"></i><br>Transactions</a>
            <a href="budget.php" class="menu-card"><i class="fas fa-coins"></i><br>Budgets</a>
            <a href="summary.php" class="menu-card"><i class="fas fa-chart-pie"></i><br>Reports</a>
            <a href="logout.php" class="menu-card" style="border-color: #ccc;"><i class="fas fa-sign-out-alt"></i><br>Logout</a>
        </div>

        <div class="content-area">
            <div class="welcome-text">
                WELCOME BACK, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 🐾
            </div>

            <table class="stats-table">
                <tr>
                    <td class="stats-header">QUICK STATISTICS</td>
                    <td>
                        Total Income: <strong style="color: green;">RM <?php echo number_format($totalIncome, 2); ?></strong><br>
                        Total Spent: <strong style="color: red;">RM <?php echo number_format($totalSpent, 2); ?></strong><br>
                        <hr style="border: 0.5px solid #FFE4E1;">
                        Net Savings: <strong style="font-size: 1.5rem; color: <?php echo $savings < 0 ? 'red' : '#5D4037'; ?>;">
                            RM <?php echo number_format($savings, 2); ?></strong>
                    </td>
                </tr>
            </table>

            <div class="graph-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Budget', 'Income', 'Spent'],
                datasets: [{
                    label: 'RM Amount',
                    data: [<?php echo $budget; ?>, <?php echo $totalIncome; ?>, <?php echo $totalSpent; ?>],
                    backgroundColor: ['#FFDAB9', '#B2FFB2', '#FFB6C1'],
                    borderColor: ['#FFCCBC', '#81C785', '#FF80AB'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true,
                         ticks: { font: { size: 16 } } 
                    },
                    x: { ticks: { font: { size: 16 } }
                    }
                },
                plugins: {
                    legend: { 
                        labels: { font: { size: 18, family: 'VT323' } }
                    }
                }
            }
        });
    </script>
</body>
</html>