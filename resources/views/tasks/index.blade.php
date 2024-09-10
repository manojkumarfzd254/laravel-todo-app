<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP - Simple To Do List App</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">PHP - Simple To Do List App</h2>
    <div class="input-group mb-3">
        <input type="text" id="task-title" class="form-control" placeholder="Add Task" aria-label="Add Task">
        <div class="input-group-append">
            <button class="btn btn-primary" id="add-task" type="button">Add Task</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="statusResp"></div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Task</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="task-list">
            @foreach ($tasks as $task)
                <tr id="task-{{ $task->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->status ? 'Done' : 'Pending' }}</td>
                    <td>
                        <button class="btn btn-success btn-sm toggle-status" data-id="{{ $task->id }}">
                            &#x2714;
                        </button>
                        <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">
                            &#x2716;
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        // Add Task
        $('#add-task').click(function () {
            var title = $('#task-title').val();
            if (title === '') {
                alert('Task title is required!');
                return;
            }
    
            $.ajax({
                url: '/tasks',
                type: 'POST',
                data: {
                    title: title,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if(response.status == true) {
                        $('#statusResp').empty().html('<div class="alert alert-success">'+response.success+'</div>');
                        $('#task-title').val('');
                    
                        $('#task-list').append(
                            `<tr id="task-${response.data.id}">
                                <td>${response.data.id}</td>
                                <td>${response.data.title}</td>
                                <td>${response.data.status ? 'Done' : 'Pending'}</td>
                                <td>
                                    <button class="btn btn-success btn-sm toggle-status" data-id="${response.data.id}">&#x2714;</button>
                                    <button class="btn btn-danger btn-sm delete-task" data-id="${response.data.id}">&#x2716;</button>
                                </td>
                            </tr>`
                        );
                        attachEventListeners();
                    } else {
                        $('#statusResp').empty().html('<div class="alert alert-danger">Error in creating task!!</div>');
                    }
                    
                },
                error: function (response) {
                    alert(response.responseJSON.errors.title[0]);
                }
            });
        });
    
        // Toggle Task Status
        function attachEventListeners() {
            $('.toggle-status').off('click').on('click', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '/tasks/' + id,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if(response.status == true) {
                            $('#statusResp').empty().html('<div class="alert alert-info"><b>'+response.data.title+'</b> task successfully updated.</div>');
                            $(`#task-${id} td:nth-child(3)`).text(response.data.status ? 'Done' : 'Pending');
                        } else {
                            $('#statusResp').empty().html('<div class="alert alert-danger">Error in updating task!!</div>');
                        }
                    }
                });
            });
    
            // Delete Task
            $('.delete-task').off('click').on('click', function () {
                if (confirm('Are you sure to delete this task?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '/tasks/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if(response.status == true) {
                                $('#statusResp').empty().html('<div class="alert alert-warning"><b>'+response.title+'</b> '+response.success+'</div>');
                                $('#task-' + id).remove();
                            } else {
                                $('#statusResp').empty().html('<div class="alert alert-danger">Error in deleting task!!</div>');
                            }
                        }
                    });
                }
            });
        }
    
        // Initial attachment of event listeners
        attachEventListeners();
    });
    </script>
    
</body>
</html>
