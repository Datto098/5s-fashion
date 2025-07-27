<!DOCTY    <!-- Admin CSS - Using BASE_URL constant -->
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : 'http://localhost/5s-fashion' ?>/public/assets/css/admin.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"&gt;l>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '5S Fashion Admin' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Admin CSS - Using BASE_URL constant -->
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : 'http://localhost/5s-fashion' ?>/public/assets/css/admin-complete.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <?php if (isset($additionalCSS)): ?>
        <style><?= $additionalCSS ?></style>
    <?php endif; ?>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include APP_PATH . '/views/admin/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <?php include APP_PATH . '/views/admin/layouts/topbar.php'; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <!-- Messages -->
                <?php include APP_PATH . '/views/admin/layouts/messages.php'; ?>

                <!-- Main Content -->
                <?= $content ?>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>

    <!-- Common Admin Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("collapsed");
                });
            }

            // Add fade-in animation
            document.querySelector('.admin-content').classList.add('fade-in');
        });
    </script>
</body>
</html>
