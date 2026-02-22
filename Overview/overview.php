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
$expenses = [];
$db_connected = false;
$all_expenses_data = []; // Array to pass to JavaScript

// --- 1. SAFE DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true; 
    
    // FETCH EXPENSES DATA FOR THE CALENDAR POPUP
    // *Note: Ensure your actual date column in DB is named 'date'. Change 'date' below if it's named 'created_at', etc.
    $stmt = $pdo->query("SELECT DATE(date) as exp_date, description FROM expenses");
    $all_expenses_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_connected = false; 
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
    
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 20px;
        }
        .close-btn:hover { color: black; }
        .modal-title {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .modal-desc-box {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            overflow-y: auto;
            box-sizing: border-box;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .modal-actions button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 15px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .modal-actions button:hover {
            background-color: #333;
        }
        /* Make calendar days look clickable */
        .calendar-grid div { cursor: pointer; } 
    </style>
</head>
<body>

    <div class="container">
        <aside class="sidebar">
            <div class="brand">
            <a href="/Dashboard/dash.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                 <img src="logo.png" alt="Spendify" class="logo-img" onerror="this.style.display='none'; this.parentNode.innerHTML='<h2 style=\'color:#4A3B80;\'><i class=\'fa-solid fa-wallet\'></i> Spendify</h2>'">
            </a>
            </div>
            <nav class="side-nav">
                <ul>
                    <li><a href="/Dashboard/dash.php"><img src="dashboard.png" alt="Dashboard"> Dashboard</a></li>
                    <li><a href="/Expenses/expense.php"><img src="sideexpense.png" alt="Expense"> Expense</a></li>
                    <li class="active"><a href="/Overview/overview.php"><img src="overvirew.png" alt="Overview"> Overview</a></li>
                </ul>
            </nav>

            <div class="side-footer">
                <ul>
                    <li><a href="/Settings/settings.php"><img src="settings.png" alt="Settings"> Settings</a></li>
                </ul>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <img src="Menu.png" alt="Menu" style="width: 30px; cursor: pointer;">
                    <h1>Overview</h1>
                </div>
                <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass" style="color: #999;"></i>
                <input type="text" placeholder="Search">
                </div>
                <div class="user-profile">
                <a href="/Settings/settings.php">
                    <img src="profile.png" alt="Profile" class="avatar">
                </a>
            </div>
            </header>

            <section class="stats-grid">
                <div class="card">
                    <div class="card-info">
                        <h3>Current Balance</h3>
                        <p>233,000</p>
                    </div>
                    <div class="card-icon">
                        <img src="current.png" alt="Balance">
                    </div>
                </div>

                <div class="card">
                    <div class="card-info">
                        <h3>Total Income</h3>
                        <p>233,000</p>
                    </div>
                    <div class="card-icon">
                        <img src="total.png" alt="Income">
                    </div>
                </div>

                <div class="card">
                    <div class="card-info">
                        <h3>Total Expenses</h3>
                        <p>233,000</p>
                    </div>
                    <div class="card-icon">
                        <img src="total expense.png" alt="Expense">
                    </div>
                </div>

                <div class="card">
                    <div class="card-info">
                        <h3>Savings</h3>
                        <p>233,000</p>
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
                    
                    <div class="calendar-grid">
                        </div>
                </div>
            </section>
        </main>
    </div>

    <div id="descModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModalBtn">&times;</span>
            <div class="modal-title">Description</div>
            <div class="modal-desc-box" id="modalDescContent">
                </div>
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