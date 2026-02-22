<?php
// --- CONFIGURATION ---
$host = 'localhost';
$dbname = 'spendify_db'; 
$user = 'root'; 
$pass = '';      

// --- INITIALIZE DEFAULTS ---
$today_expense = 0;
$week_expense = 0;
$month_expense = 0;
$total_expense = 0; 
$db_connected = false;

// --- DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true; 
} catch (PDOException $e) {
    $db_connected = false; 
    die("Database connection failed: ".$e->getMessage());
}

// --- DATA CALCULATIONS ---
if ($db_connected) {
    function getSum($pdo, $interval = null) {
        $sql = "SELECT SUM(amount) as total FROM expenses";
        if ($interval === 'today') $sql .= " WHERE expense_date = CURDATE()";
        elseif ($interval === 'week') $sql .= " WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        elseif ($interval === 'month') $sql .= " WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    $today_expense = getSum($pdo, 'today');
    $week_expense = getSum($pdo, 'week');
    $month_expense = getSum($pdo, 'month');
    $total_expense = getSum($pdo);
}

// --- BAR CHART DATA (weekly) ---
$weeklyLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
$weeklyData = [0,0,0,0,0,0,0];

if ($db_connected) {
    $stmt = $pdo->query("
        SELECT WEEKDAY(expense_date) as day_index, SUM(amount) as total
        FROM expenses
        WHERE YEARWEEK(expense_date,1) = YEARWEEK(CURDATE(),1)
        GROUP BY day_index
    ");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $weeklyData[(int)$row['day_index']] = (float)$row['total'];
    }
}

// --- PIE CHART DATA (today by category) ---
$categoryMap = [
    'Bills'=>'#CCFF33','Household'=>'#33404D','Education'=>'#FF9900',
    'Personal care'=>'#FFB3B3','Entertainment'=>'#B3C6FF','Saving'=>'#CC99FF',
    'Fashion'=>'#800040','Transport'=>'#FF3366','Health'=>'#00B33C','Other'=>'#00FFFF'
];

$catLabels = [];
$catData = [];
$catColors = [];

if ($db_connected) {
    $stmt = $pdo->query("
        SELECT category_id, SUM(amount) as total 
        FROM expenses 
        WHERE expense_date = CURDATE()
        GROUP BY category_id
    ");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmtCat = $pdo->prepare("SELECT category_name FROM categories WHERE category_id=?");
        $stmtCat->execute([$row['category_id']]);
        $catName = $stmtCat->fetchColumn() ?: 'Unknown';

        $catLabels[] = $catName;
        $catData[] = (float)$row['total'];
        $catColors[] = $categoryMap[$catName] ?? '#'.substr(md5($catName),0,6);
    }
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
            <li><a href="/Dashboard/dash.php" class="active"><img src="dashboard.png" class="menu-icon"> Dashboard</a></li>
            <li><a href="/Expenses/expense.php"><img src="expense.png" class="menu-icon"> Expense</a></li>
            <li><a href="/Overview/overview.php"><img src="overview.png" class="menu-icon"> Overview</a></li>
            <li><a href="/Settings/settings.php"><img src="settings.png" class="menu-icon"> Settings</a></li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <header>
        <div class="header-left">
            <img src="Menu.png" alt="Menu" style="width:30px;cursor:pointer;">
            <h1>Dashboard</h1>
        </div>
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass" style="color:#999;"></i>
            <input type="text" placeholder="Search">
        </div>
        <div class="user-profile">
            <a href="/Settings/settings.php"><img src="profile.png" alt="Profile" class="avatar"></a>
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
            <div class="chart-title">Weekly Expense Chart</div>
            <canvas id="barChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-title">Today's Category Breakdown</div>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<script>
const weeklyLabels = <?php echo json_encode($weeklyLabels); ?>;
const weeklyData = <?php echo json_encode($weeklyData); ?>;

const catLabels = <?php echo json_encode($catLabels); ?>;
const catData = <?php echo json_encode($catData); ?>;
const catColors = <?php echo json_encode($catColors); ?>;

// Bar chart
new Chart(document.getElementById('barChart'), {
    type:'bar',
    data:{
        labels: weeklyLabels,
        datasets:[{label:'This Week Expenses', data:weeklyData, backgroundColor:'#4A3B80'}]
    },
    options:{responsive:true}
});

// Pie chart
new Chart(document.getElementById('pieChart'), {
    type:'pie',
    data:{
        labels: catLabels,
        datasets:[{data:catData, backgroundColor:catColors}]
    },
    options:{responsive:true}
});
</script>

</body>
</html>