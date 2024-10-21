<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

// Query to fetch completed tasks (status = 'Done')
$sql = "SELECT title, created_at, completed_at FROM tasks WHERE user_id='$user_id' AND status='Done'";
$result = $conn->query($sql);

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

// Return completed tasks as JSON
echo json_encode($tasks);
?>
