<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all tasks for the user, including completed tasks
$sql = "SELECT title, description, status, created_at, completed_at FROM tasks WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

$tasks = [];

if ($result->num_rows > 0) {
    while ($task = $result->fetch_assoc()) {
        $tasks[] = $task;
    }
}

// Return tasks as JSON
echo json_encode($tasks);
?>
