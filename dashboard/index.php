<?php include 'partials/header.php'; ?>

<?php include 'partials/sidebar.php'; ?>

<main class="main-content">

    <!-- TOPBAR -->
    <div class="glass topbar">

        <div>

            <h2 class="welcome-title">
                Dashboard
            </h2>

            <p class="welcome-sub">
                DK Voice Engine Control Center
            </p>

        </div>

        <!-- RIGHT SIDE -->
        <div class="topbar-actions">

            <!-- STATUS -->
            <div class="status-box">

                <p class="status-label">
                    System Status
                </p>

                <p class="status-online">
                    ● ONLINE
                </p>

            </div>

            <!-- THEME -->
            <button id="themeToggle" class="top-btn theme-btn">
                🌙 Dark
            </button>

            <!-- LOGOUT -->
            <a href="logout.php">

                <button class="top-btn logout-top-btn">
                    ⏻ Logout
                </button>

            </a>

        </div>

    </div>

</main>

<?php include 'partials/footer.php'; ?>