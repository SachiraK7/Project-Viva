<?php
session_start();

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

// --- DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true;
} catch (PDOException $e) {
    $db_connected = false;
}

// --- API HANDLING ---
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

// --- DATA CALCULATIONS ---
if ($db_connected) {
    function getSum($pdo, $interval = null) {
        $sql = "SELECT SUM(amount) as total FROM expenses";
        if ($interval === 'today') $sql .= " WHERE date = CURDATE()";
        elseif ($interval === 'week') $sql .= " WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        elseif ($interval === 'month') $sql .= " WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    $today_expense = getSum($pdo, 'today');
    $week_expense = getSum($pdo, 'week');
    $month_expense = getSum($pdo, 'month');
    $total_expense = getSum($pdo);
    
    try {
        $expenses = $pdo->query("SELECT * FROM expenses ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $expenses = [];
    }
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
            <a href="/Dashboard/dash.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                 <img src="Logo.png" alt="Spendify" class="logo-img">
            </a>
        </div>
        <nav class="menu">
            <ul>
                <li><a href="/Dashboard/dash.php"><img src="dashboard.png" class="menu-icon" alt="Dashboard"> Dashboard</a></li>
                <li><a href="/Expenses/expense.php" class="active"><img src="expense.png" class="menu-icon" alt="Expense"> Expense</a></li>
                <li><a href="/Overview/overview.php"><img src="overview.png" class="menu-icon" alt="Overview"> Overview</a></li>
                <li><a href="/Settings/settings.php"><img src="settings.png" class="menu-icon" alt="Settings"> Settings</a></li>
            </ul>
        </nav>
    </div>

     <div class="main-content">
        <header>
            <div class="header-left">
                <img src="Menu.png" alt="Menu" style="width: 30px; cursor: pointer;">
                <h1>Expense</h1>
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
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td>$ <?php echo htmlspecialchars($row['amount']); ?></td>
                            <td>
                                <button class="btn-delete" onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" style="text-align:center; color:#888;">No expenses found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="expense-form">
                <h2>Add New Expense</h2>
                <form id="addExpenseForm">
                    <input type="hidden" name="action" value="add_expense">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="categorySelect" required>
                            <option value="" disabled selected>Choose Category</option>
                            <option value="Bills">Bills</option>
                            <option value="Education">Education</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Health">Health</option>
                            <option value="Household">Household</option>
                            <option value="Personal care">Personal care</option>
                            <option value="Saving">Saving</option>
                            <option value="Transport">Transport</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount</label>
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
    
    <div id="categoryModal" class="modal">
        <div class="modal-content" style="text-align: left;">
            <span class="close" onclick="closeModal('categoryModal')">&times;</span>
            <h3 style="margin-top: 0; font-size: 16px; margin-bottom: 20px;">Add Category</h3>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" id="newCategoryInput" style="border: 1px solid #ccc;">
            </div>
            <div class="modal-buttons" style="justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-cancel" style="background: #000; color: #fff; padding: 8px 20px;" onclick="closeModal('categoryModal')">Cancel</button>
                <button type="button" class="btn-confirm" style="background: #000; padding: 8px 20px;" onclick="addNewCategory()">Add Category</button>
            </div>
        </div>
    </div>
    
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <p style="margin-top: 10px; font-weight: bold;">Are you sure you want to delete this expense?</p>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn-confirm" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    
    <script src="expense.js"></script>
</body>
</html>