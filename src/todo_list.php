 <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    // Toggle Checkbox for to-do item
    function updateToDoItem(itemID, status) {
        $.post("update_todo_item.php", {
            item_ID: itemID,
            status: status
        }, function(response) {
            getToDoList();
        });
    }

    function getToDoList() {
        $.post("get_todo_list.php", {
            user_ID: <?php echo $_SESSION["user_ID"] ?>
        }, function(response) {
            response = JSON.parse(response);
            var html = "";
            for (var i = 0; i < response.length; i++) {
                var todoitem = response[i];
                html += "<label class='list-group-item d-flex gap-3'>";
                // If the task is complete, check the checkbox
                if (todoitem["status"] == 1) {
                    html += "<input class='form-check-input flex-shrink-0' type='checkbox' value='' checked style='font-size: 1.375em;' onclick='updateToDoItem(" + todoitem["item_ID"] + ", 0)'>";
                } else {
                    html += "<input class='form-check-input flex-shrink-0' type='checkbox' value='' style='font-size: 1.375em;' onclick='updateToDoItem(" + todoitem["item_ID"] + ", 1)'>";
                }
                html += "<span class='pt-1 form-checked-content'>";
                html += "<strong>" + todoitem["title"] + "</strong>";
                html += "<small class='d-block text-body-secondary'>";
                html += "<svg class='bi me-1' width='1em' height='1em'>";
                html += "<use xlink:href='#calendar-event'></use>";
                html += "</svg>";
                html += todoitem["due_date"];
                html += "</small>";
                html += "</span>";
                html += "</label>";
            }
            $("#todo-list").html(html);
            // Reset dark mode for new elements
            setDarkMode();
        });
    }
    $(document).ready(() => {
        getToDoList();
        // Override form for adding new to-do item
        $("#addToDoItem").submit((e) => {
            e.preventDefault();
            $.post("create_todo_item.php", {
                user_ID: <?php echo $_SESSION["user_ID"] ?>,
                title: $("#title").val(),
                due_date: $("#due_date").val()
            }, function(response) {
                getToDoList();
                // Wipe the form
                $("#addToDoItem").trigger("reset");
            });
        });
    })
</script>

<h1 class="my-5">Todo List</h1>
<div class="d-flex flex-column flex-md-row py-md-10 align-items-center justify-content-center" style="width:100%;">
    <div class="list-group" style="width:100%;">
        <div id="todo-list">
        </div>
        <form id="addToDoItem">
            <label class="list-group-item d-flex gap-3 bg-body-tertiary">
                <input class="form-check-input form-check-input-placeholder bg-body-tertiary flex-shrink-0 pe-none" disabled="" type="checkbox" value="" style="font-size: 1.375em;">
                <span class="pt-1 form-checked-content">
                    <span class="w-100"><input class="form-control" type="text" name="title" id="title" placeholder="Add new task..." required></span>
                    <small class="d-block text-body-secondary">
                        <svg class="bi me-1" width="1em" height="1em">
                            <use xlink:href="#list-check"></use>
                        </svg>
                        <input class="form-control" type="datetime-local" id="due_date" name="due_date" required>
                    </small>
                </span>
                <button type="submit" class="btn btn-primary">Add</button>
            </label>
        </form>
    </div>
</div>