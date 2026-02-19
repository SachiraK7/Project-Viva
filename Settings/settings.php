<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spendify - Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>

<div class="sidebar">
    <h2>Spendify</h2>

    <ul>
        <li>Dashboard</li>
        <li>Expense</li>
        <li>Overview</li>
        <li class="active">Settings</li>
    </ul>
</div>

<div class="main">

    <div class="topbar">
        <h2>Settings</h2>
        <div class="user-name">Simon Riley</div>
    </div>

    <div class="content">

        <!-- Left Profile -->
        <div class="profile-section">
            <div class="profile-card">
                <div class="avatar"></div>
                <h3>Simon Riley</h3>

                <button class="btn-light">Privacy Policy</button>
                <button class="btn-light">Terms & Conditions</button>
                <button class="btn-light">Logout</button>
                <button class="btn-danger">Delete Account</button>
            </div>
        </div>

        <!-- Right Settings -->
        <div class="settings-section">

            <h3>Profile Information</h3>

            <div class="input-group">
                <label>Full Name</label>
                <div class="input-box">
                    <input type="text" value="Simon Riley" readonly>
                    <a href="#">Change name ></a>
                </div>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <div class="input-box">
                    <input type="text" value="simon@email.com" readonly>
                    <a href="#">Change email ></a>
                </div>
            </div>

            <h3 class="security-title">Security</h3>

            <div class="input-group">
                <label>Password</label>
                <div class="input-box">
                    <input type="password" value="12345678" readonly>
                    <a href="#">Change password ></a>