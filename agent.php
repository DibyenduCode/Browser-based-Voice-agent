<?php
session_start();

if (!isset($_SESSION["access"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>DK Voice Agent</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="./assets/agent/styles.css">

</head>

<body>

<a href="login.php" class="admin-btn">
    Admin
</a>

<div class="center">

    <div class="orb-container">

        <div class="wave" id="wave"></div>

        <div class="ball" id="ball"></div>

    </div>

    <div class="status" id="status">
        STARTING...
    </div>

</div>

<!-- =========================================
     CHAT UI
========================================= -->

<div class="chat-wrapper">

    <div class="chat-header">
        DK AI ASSISTANT
    </div>

    <div class="chat-body" id="chatBody"></div>

    <div class="chat-input-area">

        <input
            type="text"
            id="chatInput"
            class="chat-input"
            placeholder="Type your command..."
            autocomplete="off"
        >

        <button
            id="sendBtn"
            class="send-btn"
        >
            Send
        </button>

    </div>

</div>
<script src="./assets/agent/script.js"></script>
</body>
</html>