<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];

// Handle adding a new task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    // Ensure the task is added with a default status of "To Do"
    $sql = "INSERT INTO tasks (title, description, status, user_id) VALUES ('$title', '$description', 'To Do', '$user_id')";
    $conn->query($sql);
    
    // Redirect to prevent form resubmission
    header('Location: dashboard.php');
    exit();
}

// Fetch all tasks for the user
$tasks = $conn->query("SELECT * FROM tasks WHERE user_id='$user_id'");

// Store tasks in an associative array for easier access
$taskArray = [];
while ($task = $tasks->fetch_assoc()) {
    $taskArray[$task['status']][] = $task;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        h2, h3 {
            color: #333;
        }

        /* Center content on the page */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Form styling */
        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form input[type="text"], form textarea {
            flex: 1;
            padding: 10px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        /* Kanban board layout */
        .kanban-board {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .column {
            flex: 1;
            background-color: #e2e2e2;
            padding: 10px;
            border-radius: 8px;
            min-height: 300px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .column h3 {
            text-align: center;
            color: #4CAF50;
        }

        .task {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
            cursor: grab;
        }

        .task h4 {
            margin: 0;
            font-size: 16px;
        }

        .task p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        /* Task being dragged */
        .dragging {
            opacity: 0.5;
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <h2>Task Management</h2>

    <form method="POST" action="dashboard.php">
        <input type="text" name="title" placeholder="Task Title" required>
        <textarea name="description" placeholder="Task Description"></textarea>
        <button type="submit">Add Task</button>
    </form>
    <button id="historyBtn" style="position: fixed; top: 10px; right: 10px;">History</button>
<div id="historyModal" style="display:none; position: fixed; top: 50px; right: 50px; background-color: white; padding: 20px; border: 1px solid black; z-index: 1000;">
    <h3>Task History</h3>
    <ul id="historyList"></ul>
    <button id="closeHistory">Close</button>
</div>


    <div class="kanban-board">
        <div class="column" id="todo">
            <h3>To Do</h3>
            <?php if (isset($taskArray['To Do'])): ?>
                <?php foreach ($taskArray['To Do'] as $task): ?>
                    <div class="task" draggable="true" data-task-id="<?= $task['id'] ?>">
                        <h4><?= $task['title'] ?></h4>
                        <p><?= $task['description'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="column" id="in-progress">
            <h3>In Progress</h3>
            <?php if (isset($taskArray['In Progress'])): ?>
                <?php foreach ($taskArray['In Progress'] as $task): ?>
                    <div class="task" draggable="true" data-task-id="<?= $task['id'] ?>">
                        <h4><?= $task['title'] ?></h4>
                        <p><?= $task['description'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="column" id="done">
            <h3>Done</h3>
            <?php if (isset($taskArray['Done'])): ?>
                <?php foreach ($taskArray['Done'] as $task): ?>
                    <div class="task" draggable="true" data-task-id="<?= $task['id'] ?>">
                        <h4><?= $task['title'] ?></h4>
                        <p><?= $task['description'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="drag-drop.js"></script>
    <script>
        // Drag and drop functionality
        const tasks = document.querySelectorAll('.task');
        const columns = document.querySelectorAll('.column');

        tasks.forEach(task => {
            task.addEventListener('dragstart', () => {
                task.classList.add('dragging');
            });

            task.addEventListener('dragend', () => {
                task.classList.remove('dragging');
            });
        });

        columns.forEach(column => {
            column.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            column.addEventListener('drop', (e) => {
                const taskId = e.dataTransfer.getData('text/plain');
                const task = document.querySelector(`[data-task-id='${taskId}']`);
                const newStatus = column.id === 'todo' ? 'To Do' : column.id === 'in-progress' ? 'In Progress' : 'Done';

                column.appendChild(task);
                updateTaskStatus(taskId, newStatus);
            });
        });

        function updateTaskStatus(taskId, newStatus) {
            fetch('update_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: taskId, status: newStatus }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Task updated:', data);
            })
            .catch((error) => {
                console.error('Error updating task:', error);
            });
        }
    </script>
</body>
</html>
