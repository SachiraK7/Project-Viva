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
                <img src="Logo.png" alt="Spendify Logo" class="brand-logo">
                <h2>Spendify</h2>
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
                    </a></li>
                    
                    <li><a href="logout.php">
                        <img src="logout.png" alt="Logout"> Logout
                    </a></li>
                    
                </ul>
            </div>
        </aside>

        <main class="main-content">
            
            <header class="top-header">
                <div class="header-title">
                    <img src="Menu.png" class="menu-toggle" alt="Menu" style="width: 30px; cursor: pointer;">
                    <h1>Overview</h1>
                </div>
                <div class="user-profile">
                    <span><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'User'; ?></span>
                    <div class="avatar">
                        <img src="profile.png" alt="User">
                    </div>
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
                        <img src="expense.png" alt="Expense">
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