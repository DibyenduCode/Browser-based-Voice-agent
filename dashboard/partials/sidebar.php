<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar glass">

    <div class="sidebar-top">

        <div class="logo-box">

            <h1 class="logo-text">
                DK ENGINE
            </h1>

            <p class="logo-sub">
                Secure Admin Dashboard
            </p>

        </div>

        <!-- PROFILE -->
        <!-- <div class="glass profile-card">

            <div class="avatar">
                👤
            </div>

            <h2 class="profile-name">
                Dibyendu
            </h2>

            <p class="profile-role">
                Super Administrator
            </p>

        </div> -->

        <!-- MENU -->
        <nav class="menu">

            <a href="index.php" class="menu-item <?= ($current == 'index.php') ? 'active' : '' ?>">
                🏠 Dashboard
            </a>

            <a href="profile.php" class="menu-item <?= ($current == 'profile.php') ? 'active' : '' ?>">
                👤 Profile
            </a>

            <a href="pin.php" class="menu-item <?= ($current == 'pin.php') ? 'active' : '' ?>">
                🔐 Change PIN
            </a>

            <a href="name.php" class="menu-item <?= ($current == 'name.php') ? 'active' : '' ?>">
                ✏ Change Name
            </a>

            <a href="webhook.php" class="menu-item <?= ($current == 'webhook.php') ? 'active' : '' ?>">
                🌐 Webhook URL
            </a>

        </nav>

</aside>