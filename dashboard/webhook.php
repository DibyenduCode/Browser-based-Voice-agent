<?php include 'partials/header.php'; ?>

<?php include 'partials/sidebar.php'; ?>

<main class="main-content">

    <div class="glass settings-card">

        <h3 class="card-title">
            🌐 Webhook Configuration
        </h3>

        <div class="form-group">

            <input 
                type="text"
                value="https://your-webhook-url.com"
                class="input"
            >

            <textarea 
                rows="5"
                class="input textarea"
                placeholder="Webhook description..."
            ></textarea>

            <button class="btn">
                Save Webhook
            </button>

        </div>

    </div>

</main>

<?php include 'partials/footer.php'; ?>