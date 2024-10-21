<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['id'];
    $new_status = $data['status'];

    // Update task status based on where it was dropped
    if ($new_status == 'done') {
        // Set completed_at timestamp when task is moved to "Done"
        $sql = "UPDATE tasks SET status='$new_status', completed_at=CURRENT_TIMESTAMP WHERE id='$task_id'";
    } else {
        // Just update the status for other columns
        $sql = "UPDATE tasks SET status='$new_status', completed_at=NULL WHERE id='$task_id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Task status updated successfully']);
    } else {
        echo json_encode(['message' => 'Error updating task status']);
    }
}
?>
