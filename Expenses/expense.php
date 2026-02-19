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
        if ($action === 'update_expense') {
            $stmt = $pdo->prepare("UPDATE expenses SET date=?, category=?, amount=?, description=? WHERE id=?");
            $result = $stmt->execute([$_POST['date'], $_POST['category'], $_POST['amount'], $_POST['description'], $_POST['id']]);
            echo json_encode(['success' => $result]);
            exit;
        }
        if ($action === 'get_expense') {
            $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
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

    try {
        $expenses = $pdo->query("SELECT * FROM expenses ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $expenses = []; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense - Spendify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="expense.css">
</head>
<body>

    <div class="sidebar">
       <div class="brand">
            <a href="dashboard.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                 <img src="logo.png" alt="Spendify" class="logo-img" onerror="this.style.display='none'; this.parentNode.innerHTML='<h2 style=\'color:#4A3B80; display:flex; gap:10px; align-items:center;\'><i class=\'fa-solid fa-wallet\'></i> Spendify</h2>'">
            </a>
        </div>
        
        <nav class="menu">
            <ul>
                <li>
                    <a href="dashboard.php">
                        <img src="dashboard.png" class="menu-icon" alt="Dashboard">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="expense.php" class="active">
                        <img src="expense.png" class="menu-icon" alt="Expense">
                        Expense
                    </a>
                </li>
                <li>
                    <a href="analytics.php">
                        <img src="overview.png" class="menu-icon" alt="Overview">
                        Overview
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <img src="settings.png" class="menu-icon" alt="Settings">
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <i class="fa-solid fa-bars" style="margin-right: 15px; cursor: pointer;"></i> Expense
            </div>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="user-profile">
                <span><?php echo $userName; ?></span>
                <a href="settings.php">
                    <img src="profile.png" alt="Profile" class="avatar">
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="card">
                <div class="card-info">
                    <h3>Today Expense</h3>
                    <h1>$ <?php echo number_format($today_expense); ?></h1>
                </div>
                <div class="card-icon">
                    <img src="today.png" onerror="this.outerHTML='<i class=\'fa-solid fa-calendar-day\' style=\'font-size:30px; color:#9A77FF\'></i>'">
                </div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>Last week Expense</h3>
                    <h1>$ <?php echo number_format($week_expense); ?></h1>
                </div>
                <div class="card-icon">
                    <img src="last_week.png" onerror="this.outerHTML='<i class=\'fa-solid fa-calendar-week\' style=\'font-size:30px; color:#9A77FF\'></i>'">
                </div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>Last 30 day Expense</h3>
                    <h1>$ <?php echo number_format($month_expense); ?></h1>
                </div>
                <div class="card-icon">
                    <img src="last_month.png" onerror="this.outerHTML='<i class=\'fa-regular fa-calendar\' style=\'font-size:30px; color:#9A77FF\'></i>'">
                </div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>Total Expense</h3>
                    <h1>$ <?php echo number_format($total_expense); ?></h1>
                </div>
                <div class="card-icon">
                    <img src="total.png" onerror="this.outerHTML='<i class=\'fa-solid fa-sack-dollar\' style=\'font-size:30px; color:#9A77FF\'></i>'">
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="table-container">
                <div class="table-header">All Expenses</div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        if (!empty($expenses)):
                            foreach($expenses as $row): 
                        ?>
                        <tr class="data-row" data-id="<?php echo $row['id']; ?>">
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars(str_replace('-', '.', $row['date'])); ?></td>
                            <td>$ <?php echo htmlspecialchars($row['amount']); ?></td>
                            <td onclick="event.stopPropagation()">
                                <button class="btn-delete" onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        else:
                        ?>
                            <tr><td colspan="5" style="text-align:center; color:#888;">No expenses found (Database empty or not connected)</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="expense-form">
                <h2>Add New Expense</h2>
                <form id="addExpenseForm">
                    <input type="hidden" name="action" value="add_expense">
                    
                    <div class="form-group">
                        <label>Date of Expense</label>
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <div class="cat-group">
                            <select id="categorySelect" name="category">
                                <option>Choose Category</option>
                                <option>Bills</option>
                                <option>Education</option>
                                <option>Entertainment</option>
                                <option>Fashion</option>
                                <option>Health</option>
                                <option>Household</option>
                                <option>Personal Care</option>
                                <option>Saving</option>
                                <option>Transport</option>
                                <option>Other</option>
                            </select>
                            <button type="button" class="btn-add-cat" onclick="openCategoryModal()">+ Add</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cost of Item</label>
                        <input type="number" name="amount" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" required>
                    </div>

                    <button type="submit" class="btn-submit">Add</button>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Do you want delete ?</h3>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button id="confirmDeleteBtn" class="btn-confirm">Delete</button>
            </div>
        </div>
    </div>

    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('categoryModal')">&times;</span>
            <h3>Add Category</h3>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" id="newCategoryInput">
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('categoryModal')">Cancel</button>
                <button class="btn-submit" onclick="addNewCategory()" style="margin:0; background:black;">Add Category</button>
            </div>
        </div>
    </div>

    <div id="updateModal" class="modal">
        <div class="modal-content" style="width: 400px; text-align: left;">
            <span class="close" onclick="closeModal('updateModal')">&times;</span>
            <h3 style="text-align: center;">Update Expense</h3>
            <form id="updateExpenseForm">
                <input type="hidden" name="action" value="update_expense">
                <input type="hidden" name="id" id="update_id">
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" id="update_date" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="update_category">
                        <option>Bills</option>
                        <option>Education</option>
                        <option>Entertainment</option>
                        <option>Fashion</option>
                        <option>Health</option>
                        <option>Household</option>
                        <option>Personal Care</option>
                        <option>Saving</option>
                        <option>Transport</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" id="update_amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" id="update_description" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="btn-submit" style="margin: 0;">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script src="expense.js"></script>
</body>
</html>