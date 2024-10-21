
<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$task_id = $data['taskId'];
$status = $data['status'];

$sql = "UPDATE tasks SET status = '$status' WHERE id = '$task_id'";
$conn->query($sql);

echo json_encode(['success' => true]);
?>
