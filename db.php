<?php
/**
 * Connect to the database.
 *
 * @param string $db_password The database password.
 * @return PDO The database connection object.
 */
function connectToDatabase($db_password) {
    // Database credentials
    $db_host = 'localhost';
    $db_name = 'people';
    $db_user = 'web';

    // Debug: Display database credentials (for debugging purposes only, remove in production)
    error_log("Database credentials: host=$db_host, dbname=$db_name, user=$db_user");

    // Connect to the database
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        error_log("Database connection successful.");
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

/**
 * Fetch a record by ID.
 *
 * @param PDO $pdo The database connection object.
 * @param int $id The record ID.
 * @return array|null The record data, or null if not found.
 */
function fetchRecord($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM people WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>