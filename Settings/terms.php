<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Spendify</title>
    <link rel="stylesheet" href="settings.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600&family=Poppins:wght@700&family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .policy-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .policy-content h2 {
            font-family: 'Roboto Slab', serif;
            margin-top: 30px;
            color: #333;
            font-size: 20px;
        }
        .policy-content p {
            font-family: 'Manrope', sans-serif;
            line-height: 1.8;
            color: #555;
            margin-bottom: 15px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #8A56E2;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
    </style>
</head>
<body style="background: #decdf6; padding: 20px;">

    <div class="policy-container">
        <a href="settings.php" class="back-link">← Back to Settings</a>
        
        <header class="policy-header">
            <h1 style="font-family: 'Roboto Slab'; font-weight: 700; font-size: 32px;">Terms and Conditions</h1>
            <p style="font-family: 'Manrope'; color: #888;">Effective Date: February 2026</p>
        </header>

        <section class="policy-content">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing the Spendify dashboard and using our services, you agree to be bound by these terms. If you do not agree, please do not use the application.</p>

            <h2>2. User Accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your account credentials. You agree to notify us immediately of any unauthorized use of your account.</p>

            <h2>3. Account Management</h2>
            <p>Users have the right to update their profile information, including name and email, via the settings interface. Abuse of the system may lead to account suspension.</p>

            <h2>4. Termination</h2>
            <p>You may delete your account at any time using the "Delete account" feature located in your settings profile section. This action is permanent and cannot be undone.</p>
        </section>

        <footer style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px;">
            <button class="btn-confirm" onclick="window.location.href='settings.php'">Accept Terms</button>
        </footer>
    </div>

</body>
</html>