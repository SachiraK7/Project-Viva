<?php
// --- CONFIGURATION ---
$host = 'localhost';
$dbname = 'spendify_db'; 
$user = 'root'; 
$pass = '';      

// --- INITIALIZE DEFAULTS ---
$total_expense = 0; 
$total_saving = 0;
$all_expenses_data = [];
$db_connected = false;

try {
    // --- DATABASE CONNECTION ---
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true;

    // --- TOTAL EXPENSE (all categories except 'Saving') ---
    $stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses WHERE category_id != 8");
    $total_expense_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_expense = $total_expense_data['total'] ?? 0;

    // --- TOTAL SAVING (category_id = 8) ---
    $stmt = $pdo->query("SELECT SUM(amount) as saving FROM expenses WHERE category_id = 8");
    $total_saving_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_saving = $total_saving_data['saving'] ?? 0;

    // --- FETCH EXPENSES FOR CALENDAR ---
    $stmt = $pdo->query("SELECT DATE(expense_date) as exp_date, description FROM expenses");
    $all_expenses_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_connected = false;
    echo "Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify - Overview</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="overview.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <div class="brand">
            <a href="/Dashboard/dash.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                <img src="logo.png" alt="Spendify" class="logo-img">
            </a>
        </div>
        <nav class="side-nav">
            <ul>
                <li><a href="/Dashboard/dash.php"><img src="dashboard.png" alt="Dashboard"> Dashboard</a></li>
                <li><a href="/Expenses/expense.php"><img src="sideexpense.png" alt="Expense"> Expense</a></li>
                <li class="active"><a href="/Overview/overview.php"><img src="overvirew.png" alt="Overview"> Overview</a></li>
                <li><a href="/Settings/settings.php"><img src="settings.png" alt="setting"> Setting</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="header-title">
                <img src="Menu.png" alt="Menu" style="width: 30px; cursor: pointer;">
                <h1>Overview</h1>
            </div>
        </header>

        <section class="stats-grid">

            <div class="card">
                <div class="card-info">
                    <h3>Total Expenses</h3>
                    <p>$<?php echo number_format($total_expense); ?></p>
                </div>
                <div class="card-icon">
                    <img src="total expense.png" alt="Expense">
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Savings</h3>
                    <p>$<?php echo number_format($total_saving); ?></p>
                </div>
                <div class="card-icon">
                    <img src="up.png" alt="Savings">
                </div>
            </div>
        </section>

        <section class="calendar-section">
            <h2>Calendar</h2>
            <div class="calendar-container">
                <div class="calendar-header">
                    <i class="fa-solid fa-chevron-left" id="prev"></i>
                    <h3 id="month-year">January 2026</h3>
                    <i class="fa-solid fa-chevron-right" id="next"></i>
                </div>
                <div class="calendar-grid"></div>
            </div>
        </section>
    </main>
</div>

<!-- Modal for showing description when a date is clicked -->
<div id="descModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <div class="modal-title">Description</div>
        <div class="modal-desc-box" id="modalDescContent"></div>
        <div class="modal-actions">
            <button id="cancelBtn">Cancel</button>
            <button id="confirmBtn">Confirm</button>
        </div>
    </div>
</div>

<script>
    const expensesData = <?php echo json_encode($all_expenses_data); ?>;
</script>
<script src="overview.js"></script>
</body>
</html>