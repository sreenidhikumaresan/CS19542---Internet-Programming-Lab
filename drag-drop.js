/*const columns = document.querySelectorAll('.column');
const tasks = document.querySelectorAll('.task');

tasks.forEach(task => {
    task.addEventListener('dragstart', dragStart);
    task.addEventListener('dragend', dragEnd);
});

columns.forEach(column => {
    column.addEventListener('dragover', dragOver);
    column.addEventListener('drop', drop);
});

let draggedTask = null;

function dragStart() {
    draggedTask = this;
    setTimeout(() => this.style.display = 'none', 0);
}

function dragEnd() {
    setTimeout(() => {
        draggedTask.style.display = 'block';
        draggedTask = null;
    }, 0);
}

function dragOver(e) {
    e.preventDefault();
}

function drop() {
    this.append(draggedTask);
    const taskId = draggedTask.getAttribute('data-task-id');
    const newStatus = this.id === 'todo' ? 'To Do' : this.id === 'in-progress' ? 'In Progress' : 'Done';

    fetch('update_task_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${taskId}&status=${newStatus}`
    });
}

const tasks = document.querySelectorAll('.task');
const columns = document.querySelectorAll('.column');

let draggedTask = null;

// When dragging starts
tasks.forEach(task => {
    task.addEventListener('dragstart', () => {
        draggedTask = task;
        setTimeout(() => task.classList.add('dragging'), 0);
    });

    task.addEventListener('dragend', () => {
        setTimeout(() => task.classList.remove('dragging'), 0);
        draggedTask = null;
    });
});

// When dragging over columns
columns.forEach(column => {
    column.addEventListener('dragover', (e) => {
        e.preventDefault();
    });

    column.addEventListener('drop', () => {
        column.appendChild(draggedTask);

        // You can add an AJAX request here to update the task's status in the database
        const taskId = draggedTask.getAttribute('data-task-id');
        const newStatus = column.getAttribute('id');

        // Example of updating status in the database
        fetch('update_task_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ taskId: taskId, status: newStatus }),
        });
    });
});
// Example when moving task
draggedTask.addEventListener('dragend', function() {
    // Assuming you have the task ID and new status
    updateTaskStatus(taskId, 'done');
});*/
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
    column.addEventListener('dragover', e => {
        e.preventDefault();
        const draggingTask = document.querySelector('.dragging');
        column.appendChild(draggingTask);
    });

    column.addEventListener('drop', e => {
        e.preventDefault();
        const taskId = document.querySelector('.dragging').getAttribute('data-task-id');
        const newStatus = column.getAttribute('id'); // Get new column ID (status)
        
        // Send updated task status to the server
        fetch('update_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: taskId,
                status: newStatus,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error updating task:', error);
        });
    });
});

document.getElementById('historyBtn').addEventListener('click', function () {
    fetch('fetch_history.php')
        .then(response => response.json())
        .then(data => {
            const historyList = document.getElementById('historyList');
            historyList.innerHTML = ''; // Clear old history
            data.forEach(task => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${task.title}</strong><br>Created: ${task.created_at}<br>Completed: ${task.completed_at}`;
                historyList.appendChild(li);
            });
            document.getElementById('historyModal').style.display = 'block';
        });
});

document.getElementById('closeHistory').addEventListener('click', function () {
    document.getElementById('historyModal').style.display = 'none';
});


