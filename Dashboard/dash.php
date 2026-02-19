<?php
// 1. CONNECT TO DATABASE
include 'db.php'; 

// ==========================================
// 2. FETCH REAL DATA FROM DATABASE (The 4 Cards)
// ==========================================

// --- A. TODAY'S EXPENSE ---
$today_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE expense_date = CURDATE()");
$today_data = mysqli_fetch_assoc($today_query);
$today_expense = $today_data['total'] ?? 0;

// --- B. LAST WEEK EXPENSE (Last 7 Days) ---
$week_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$week_data = mysqli_fetch_assoc($week_query);
$week_expense = $week_data['total'] ?? 0;

// --- C. LAST 30 DAYS EXPENSE ---
$month_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$month_data = mysqli_fetch_assoc($month_query);
$month_expense = $month_data['total'] ?? 0;

// --- D. TOTAL EXPENSE (All Time) ---
$total_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses");
$total_data = mysqli_fetch_assoc($total_query);
$year_expense = $total_data['total'] ?? 0;


// ==========================================
// 3. USER PROFILE LOGIC
// ==========================================
$userName = "Simon Riley";
$userImageFromDB = ""; // Change this if you add image uploading later
$profileImage = !empty($userImageFromDB) ? $userImageFromDB : "assets/default-avatar.png";


// ==========================================
// 4. DYNAMIC CHART DATA (Real DB Data)
// ==========================================

// --- A. BAR CHART: Last 7 Days Data ---
$weeklyLabels = [];
$weeklyData = [];

// Loop through the last 7 days (including today)
for ($i = 6; $i >= 0; $i--) {
    $checkDate = date('Y-m-d', strtotime("-$i days"));
    $dayName   = date('D', strtotime("-$i days")); 
    
    $weeklyLabels[] = $dayName;

    // Query total for this specific day
    $dayQuery = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE expense_date = '$checkDate'");
    $dayResult = mysqli_fetch_assoc($dayQuery);
    
    $weeklyData[] = $dayResult['total'] ?? 0;
}

// --- B. PIE CHART: Data by Category ---
$catLabels = [];
$catData = [];

$catQuery = mysqli_query($conn, "SELECT category, SUM(amount) as total FROM expenses GROUP BY category");

while ($row = mysqli_fetch_assoc($catQuery)) {
    $catLabels[] = $row['category'];
    $catData[]   = $row['total'];
}

// Colors for the Pie Chart
$catColors = ['#CCFF33', '#FF9900', '#C2D1F0', '#99004D', '#16A34A', '#334455', '#FFB3B3', '#DDA0DD', '#FF3366', '#00FFFF'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <img src="assets/spendify-logo.png" alt="Spendify" class="logo-img">
        </div>

        <nav class="menu">
            <ul>
                <li><a href="dashboard.php" class="active"><img src="dashboard.png" class="menu-icon"> Dashboard</a></li>
                <li><a href="expense.php"><img src="expense.png" class="menu-icon"> Expense</a></li>
                <li><a href="analytics.php"><img src="overview.png" class="menu-icon"> Overview</a></li>
                <li><a href="settings.php"><img src = "settings.png" class="menu-icon"> Settings</a></li>
            </ul>
        </nav>

        <div class="logout-section menu">
             <ul>
                <li><a href="logout.php">Log Out</a></li>
             </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <i class="fa-solid fa-bars hamburger"></i>
                <h2>Dashboard</h2>
            </div>

            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass" style="color: #999;"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="user-profile">
                <span><?php echo $userName; ?></span>
                <a href="settings.php">
                    <img src="<?php echo $profileImage; ?>" alt="Profile" class="avatar">
                </a>
            </div>
        </header>

        <div class="cards-container">
            <div class="card">
                <div>
                    <div class="card-label">Today Expense</div>
                    <div class="card-amount">$<?php echo number_format($today_expense); ?></div>
                    <div class="card-status">
                        <i class="fa-solid fa-circle-arrow-up status-icon"></i> Up from Today
                    </div>
                </div>
                <img src="assets/card-icon-today.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Last week Expense</div>
                    <div class="card-amount">$<?php echo number_format($week_expense); ?></div>
                    <div class="card-status">
                        <i class="fa-solid fa-circle-arrow-up status-icon"></i> Up from Last 7 days
                    </div>
                </div>
                <img src="assets/card-icon-week.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Last 30 day Expense</div>
                    <div class="card-amount">$<?php echo number_format($month_expense); ?></div>
                    <div class="card-status">
                        <i class="fa-solid fa-circle-arrow-up status-icon"></i> Up from Last 30 days
                    </div>
                </div>
                <img src="assets/card-icon-month.png" class="card-img-icon">
            </div>

            <div class="card">
                <div>
                    <div class="card-label">Total Expense</div>
                    <div class="card-amount">$<?php echo number_format($year_expense); ?></div>
                    <div class="card-status">
                        <i class="fa-solid fa-circle-arrow-up status-icon"></i> Up from Year
                    </div>
                </div>
                <img src="assets/card-icon-total.png" class="card-img-icon">
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

















