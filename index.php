<?php
require 'db.php';

// Check if the password has been submitted
if (isset($_POST['db_password'])) {
    $db_password = $_POST['db_password'];

    // Connect to the database
    $pdo = connectToDatabase($db_password);

    // Fetch the first record to display
    $currentRecord = fetchRecord($pdo, 1); // Start with the first record
} else {
    // Password not submitted yet
    $currentRecord = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People Database Editor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if ($currentRecord === null): ?>
            <!-- Password Input Form -->
            <div class="password-form">
                <h2>Enter Database Password</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="db_password">Database Password:</label>
                        <input type="password" id="db_password" name="db_password" placeholder="Enter database password" required>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Left Pane: Editing Form -->
            <div class="left-pane">
                <h2>Edit Record</h2>
                <form id="edit-form">
                    <input type="hidden" id="id" name="id" value="<?= $currentRecord['id'] ?>">
                    <?php foreach ($currentRecord as $field => $value): ?>
                        <?php if ($field !== 'id'): ?>
                            <div class="form-group">
                                <label for="<?= $field ?>"><?= ucfirst(str_replace('_', ' ', $field)) ?></label>
                                <input type="text" id="<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($value) ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <button type="button" onclick="saveChanges()">Save Changes</button>
                </form>
            </div>

            <!-- Right Pane: Search and Navigation -->
            <div class="right-pane">
                <h2>Search and Navigation</h2>
                <form id="search-form">
                    <?php foreach ($currentRecord as $field => $value): ?>
                        <?php if ($field !== 'id'): ?>
                            <div class="form-group">
                                <label for="search-<?= $field ?>"><?= ucfirst(str_replace('_', ' ', $field)) ?></label>
                                <input type="text" id="search-<?= $field ?>" name="search-<?= $field ?>" placeholder="Enter search term">
                                <div class="aux-buttons">
                                    <button type="button" onclick="navigate('<?= $field ?>', 'prev')">&lt;</button>
                                    <button type="button" onclick="navigate('<?= $field ?>', 'next')">&gt;</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <button type="button" onclick="performSearch()">Search</button>
                </form>
                <div id="search-results"></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Link to the email parsing function -->
    <div class="footer">
        <a href="add-emails.html">Add Emails</a>
    </div>

    <script src="scripts.js"></script>
</body>
</html>