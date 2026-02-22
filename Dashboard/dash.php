<?php
// --- CONFIGURATION ---
$host = 'localhost';
$dbname = 'spendify'; 
$user = 'root'; 
$pass = '';      

// --- INITIALIZE DEFAULTS ---
$today_expense = 0;
$week_expense = 0;
$month_expense = 0;
$total_expense = 0; 
$expenses = [];
$db_connected = false;

// --- 1. SAFE DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true; 
} catch (PDOException $e) {
    $db_connected = false; 
}

// --- 2. API HANDLING ---
if ($db_connected && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    try {
        if ($action === 'add_expense') {
            $stmt = $pdo->prepare("INSERT INTO expenses (date, category, amount, description) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$_POST['date'], $_POST['category'], $_POST['amount'], $_POST['description']]);
            echo json_encode(['success' => $result]);
            exit;
        }
        if ($action === 'delete_expense') {
            $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
            $result = $stmt->execute([$_POST['id']]);
            echo json_encode(['success' => $result]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// --- 3. PAGE DATA CALCULATIONS ---
if ($db_connected) {
    function getSum($pdo, $interval = null) {
        try {
            $sql = "SELECT SUM(amount) as total FROM expenses";
            if ($interval === 'today') $sql .= " WHERE date = CURDATE()";
            elseif ($interval === 'week') $sql .= " WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            elseif ($interval === 'month') $sql .= " WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) { return 0; }
    }

    $today_expense = getSum($pdo, 'today');
    $week_expense = getSum($pdo, 'week');
    $month_expense = getSum($pdo, 'month');
    $total_expense = getSum($pdo);
}

// --- 4. CHART DATA FETCHING ---
$weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$weeklyData = [0, 0, 0, 0, 0, 0, 0]; 

if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT WEEKDAY(date) as day_index, SUM(amount) as total 
                             FROM expenses 
                             WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1) 
                             GROUP BY day_index");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $weeklyData[(int)$row['day_index']] = (float)$row['total'];
        }
    } catch (Exception $e) { /* Fallback */ }
}

$categoryMap = [
    'Bills' => '#CCFF33', 'Household' => '#33404D',
    'Education' => '#FF9900', 'Personal Care' => '#FFB3B3',
    'Entertainment' => '#B3C6FF', 'Saving' => '#CC99FF',
    'Fashion' => '#800040', 'Transport' => '#FF3366',
    'Health' => '#00B33C', 'Other' => '#00FFFF'
];

$catLabels = []; $catData = []; $catColors = [];

if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT category, SUM(amount) as total FROM expenses GROUP BY category");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catName = $row['category'];
            $catLabels[] = $catName;
            $catData[] = (float)$row['total'];
            $catColors[] = $categoryMap[$catName] ?? '#' . substr(md5($catName), 0, 6);
        }
    } catch (Exception $e) { /* Fallback */ }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify Dashboard</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="brand">
            <img src="logo.png" alt="Spendify" class="logo-img">
        </div>

        <nav class="menu">
            <ul>
                <li><a href="dashboard.php" class="active"><img src="dashboard.png" class="menu-icon"> Dashboard</a></li>
                <li><a href="expense.php"><img src="expense.png" class="menu-icon"> Expense</a></li>
                <li><a href="analytics.php"><img src="overview.png" class="menu-icon"> Overview</a></li>
                <li><a href="settings.php"><img src="settings.png" class="menu-icon"> Settings</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <img src="Menu.png" alt="Menu" style="width: 30px; cursor: pointer;">
                <h1>Dashboard</h1>
            </div>

            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass" style="color: #999;"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="user-profile">
                <a href="settings.php">
                    <img src="profile.png" alt="Profile" class="avatar">
                </a>
            </div>
        </header>

        <div class="cards-container">
            <div class="card">
                <div>
                    <div class="card-label">Today Expense</div>
                    <div class="card-amount">$<?php echo number_format($today_expense); ?></div>
                </div>
                <img src="today.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Last week Expense</div>
                    <div class="card-amount">$<?php echo number_format($week_expense); ?></div>
                </div>
                <img src="last_week.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Last 30 day Expense</div>
                    <div class="card-amount">$<?php echo number_format($month_expense); ?></div>
                </div>
                <img src="last_month.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Total Expense</div>
                    <div class="card-amount">$<?php echo number_format($total_expense); ?></div>
                </div>
                <img src="total.png" class="card-img-icon">
            </div>
        </div>

        <div class="charts-wrapper">
            <div class="chart-card">
                <div class="chart-title">Expense Chart</div>
                <div class="chart-canvas-box">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-canvas-box">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const weeklyLabels = <?php echo json_encode($weeklyLabels); ?>;
        const weeklyData = <?php echo json_encode($weeklyData); ?>;
        const catLabels = <?php echo json_encode($catLabels); ?>;
        const catData = <?php echo json_encode($catData); ?>;
        const catColors = <?php echo json_encode($catColors); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>