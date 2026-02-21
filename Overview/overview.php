<?php
//session_start();

// --- SECURITY CHECK ---
// If the user is NOT logged in, kick them back to the login page.
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
//}
//?>

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
            <a href="dashboard.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                 <img src="logo.png" alt="Spendify" class="logo-img" onerror="this.style.display='none'; this.parentNode.innerHTML='<h2 style=\'color:#4A3B80;\'><i class=\'fa-solid fa-wallet\'></i> Spendify</h2>'">
            </a>
            </div>
            <nav class="side-nav">
                <ul>
                    <li><a href="dashboard.php">
                        <img src="dashboard.png" alt="Dashboard"> Dashboard
                    </a></li>
                    <li><a href="expense.php">
                        <img src="sideexpense.png" alt="Expense"> Expense
                    </a></li>
                    <li class="active"><a href="overview.php">
                        <img src="overvirew.png" alt="Overview"> Overview
                    </a></li>
                </ul>
            </nav>

            <div class="side-footer">
                <ul>
                    <li><a href="settings.php">
                        <img src="settings.png" alt="Settings"> Settings
                        </a>
                    </li>
                    
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
                <a href="settings.php">
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

    <script src="overview.js"></script>
</body>
</html>