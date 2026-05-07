<?php include 'partials/header.php'; ?>

<?php include 'partials/sidebar.php'; ?>

<main class="main-content">

    <!-- TOPBAR -->
    <div class="glass topbar">

        <div>

            <h2 class="welcome-title">
                Change PIN
            </h2>

            <p class="welcome-sub">
                Secure Your Account
            </p>

        </div>

        <div class="topbar-actions">

            <div class="status-box">

                <p class="status-label">
                    System Status
                </p>

                <p class="status-online">
                    ● ONLINE
                </p>

            </div>

            <button id="themeToggle" class="top-btn theme-btn">
                🌙 Dark
            </button>

            <a href="logout.php">

                <button class="top-btn logout-top-btn">
                    ⏻ Logout
                </button>

            </a>

        </div>

    </div>

    <!-- CONTENT -->
    <div class="glass settings-card">

        <h3 class="card-title">
            🔐 Change Security PIN
        </h3>

        <div class="form-group">

            <input type="password" placeholder="Current PIN" class="input">

            <input type="password" placeholder="New PIN" class="input">

            <input type="password" placeholder="Confirm New PIN" class="input">

            <button class="btn">
                Update PIN
            </button>

        </div>

    </div>

</main>

<?php include 'partials/footer.php'; ?>