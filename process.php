<?php
require 'db.php';

// Get the action from the request
$action = $_GET['action'] ?? $_POST['action'];

// Get the database password from the request
$db_password = $_GET['db_password'] ?? $_POST['db_password'];

// Validate the password
if (empty($db_password)) {
    die(json_encode(['error' => 'Database password is required']));
}

// Connect to the database
$pdo = connectToDatabase($db_password);

// Handle the action
switch ($action) {
    case 'navigate':
        handleNavigate();
        break;
    case 'search':
        handleSearch();
        break;
    case 'save':
        handleSave();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

/**
 * Handle navigation (previous/next record)
 */
function handleNavigate() {
    global $pdo;
    $field = $_GET['field'];
    $value = $_GET['value'];
    $direction = $_GET['direction'];

    // Determine the operator and order based on the direction
    $operator = ($direction === 'next') ? '>' : '<';
    $order = ($direction === 'next') ? 'ASC' : 'DESC';

    // Fetch the adjacent record
    $stmt = $pdo->prepare("SELECT * FROM people WHERE $field $operator :value ORDER BY $field $order LIMIT 1");
    $stmt->execute(['value' => $value]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the record as JSON
    echo json_encode($record);
}

/**
 * Handle search
 */
function handleSearch() {
    global $pdo;

    // Collect all search criteria from the request
    $searchCriteria = [];
    foreach ($_GET as $key => $value) {
        if ($key !== 'action' && $key !== 'db_password' && !empty($value)) {
            $searchCriteria[$key] = $value;
        }
    }

    // Build the SQL query dynamically
    $sql = "SELECT * FROM people";
    if (!empty($searchCriteria)) {
        $conditions = [];
        foreach ($searchCriteria as $field => $value) {
            $conditions[] = "$field LIKE :$field";
        }
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    foreach ($searchCriteria as $field => $value) {
        $stmt->bindValue(":$field", "%$value%");
    }
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the records as JSON
    echo json_encode($records);
}

/**
 * Handle saving changes to a record
 */
function handleSave() {
    global $pdo;
    $id = $_POST['id'];

    // Build the SQL query dynamically based on the fields provided
    $updates = [];
    foreach ($_POST as $field => $value) {
        if ($field !== 'action' && $field !== 'id' && $field !== 'db_password') {
            $updates[] = "$field = :$field";
        }
    }

    // Prepare the SQL query
    $sql = "UPDATE people SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Bind the values
    foreach ($_POST as $field => $value) {
        if ($field !== 'action' && $field !== 'id' && $field !== 'db_password') {
            $stmt->bindValue(":$field", $value);
        }
    }
    $stmt->bindValue(':id', $id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update record']);
    }
}
?>