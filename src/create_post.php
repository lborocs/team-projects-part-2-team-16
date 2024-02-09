<?php 
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
if (!isset($_SESSION["role"])) {
    header('location: ./login.php');
}

function createPost() {
    try {
        include "db_connection.php";
        $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
    } catch (PDOException $e) {
        echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
        return false;
    }

    if (isset($_POST["postTitle"])) {
        $title = $_POST["postTitle"];
        if (strlen($title) > 255) {
            return false;
        }
    }
    if (isset($_POST["postBody"])) {
        $body = $_POST["postBody"];
        if (strlen($body) > 1000) {
            return false;
        }
    }

    $date = date("Y-m-d");

    if (isset($_POST["isNewTopic"])){
        if ($_POST["isNewTopic"]) {
            if (isset($_POST["newTopicInput"])){
                $newTopic = $_POST["newTopicInput"];
                $result = $conn->query("SELECT max(topic_ID) FROM topics");
                $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
                if ($maxID == null) {
                    $topicID = 1;
                } else {
                    $topicID = $maxID[0] + 1;
                }
                // create the topic with new ID
                $create_topic_stmt = $conn->prepare("INSERT INTO topics (topic_ID, title, Date, views, posts) VALUES (:topicID, :title, :date, 1, 1");
                $create_topic_stmt->bindParam(':topicID', $topicID, PDO::PARAM_INT);
                $create_topic_stmt->bindParam(':title', $newTopic, PDO::PARAM_STR);
                $create_topic_stmt->bindParam(':date', $date, PDO::PARAM_STR);

                if (!$create_topic_stmt->execute()) {
                    echo "<script type='text/javascript'>alert('Failed to create the new topic, post not created');</script>";
                    return false;
                }
            }
        } else if (isset($_POST["hiddentopicsearch"])){
            $topicID = $_POST["hiddentopicsearch"];
        } else {
            return false;
        }
    }
    // $result = mysqli_query($conn, "select max(post_ID) from posts;");
    // $maxID = mysqli_fetch_row($result)[0];
    $result = $conn->query("SELECT max(post_ID) FROM posts");
    $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
    if ($maxID == null) {
        $postID = 1;
    } else {
        $postID = $maxID[0] + 1;
    }
    
    if (isset($_FILES["imageInput"])){
        $file = $_FILES["imageInput"];
        $fileName = $file["name"];
        $fileTmpName = $file["tmp_name"];
        $destination = "./postImageUploads/" . basename($fileName);
        if (move_uploaded_file($fileTmpName, $destination)) {
            echo "file uploaded";
        } else {
            echo "file upload failed";
        }
    } else {
        echo ("No image file uploaded");
    }

} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    createPost();
} else {
    echo "No POST request received.";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <link rel="stylesheet" href="./searchable_dropdown.css">
</head>

<script>
    <?php
    if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}
    ?>
    
    $(document).ready(function() {
        if ("<?php echo $colour ?>" == "text-light bg-dark") {
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        }
    })
</script>

<body>
    <script>
		function settings() {
			window.location.href = "./settings_m.html";
		};
		function logout() {
			window.location.href = "./login.html";
		};
	</script>
	
    <?php 
        if(!isset($_SESSION["role"])){
            echo "<script>window.location.href='./login.php'</script>";
        }else if($_SESSION["role"] == "Manager"){
            $taskcreate = "link-dark";
            $topicview = "link-dark";
            $dashview = "link-dark";
            include "./navbar_m.php";
        }else if($_SESSION["role"] == "TL"){
            $topicview = "link-dark";
            $taskcreate = "link-dark";
            $taskview = "link-dark";
            $dashview = "link-dark";
            include "./navbar_tl.php";
        }else if($_SESSION["role"] == "Employee"){
            $topicview = "link-dark";
            $dashview = "link-dark";
            include "./navbar_e.php";
        }
    ?>

    <main class="container">
        <h1 class="my-5">Create Post</h1>
        <div class="d-flex flex-wrap">
            <form class="col-md-8" autocomplete="off" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="postTitle" class="col-auto-2 col-form-label" style="margin-left: 0px; margin-right: 0px;">Title</label>
                    <div class="col-auto-10">
                        <input type="title" class="form-control" id="postTitle" name="postTitle" placeholder="Title">
                    </div>
                </div>

                <div class="form-group row" style="margin-left: 0px; margin-right: 0px;">
                    <label for="postBody" style="padding-left: 0px;">Body</label>
                    <textarea name="postBody" class="form-control" id="postBody" rows="10"></textarea>
                </div>

                <br>

                <div class="form-group">
                    <label for="imageInput">Include a picture?</label>
                    <input name="imageInput" type="file" class="form-control" id="imageInput" accept=".png,.jpg,.jpeg,.ico" onchange=displayImage(this)>
                </div>

                <div id="imageContainer" style="margin: 10px auto; display: none; width: 50%">
                    <p style="margin: 5px;"><b><u>Image Preview</u></b></p>
                    <div style="border: 10px solid #797676; border-radius: 17px; text-align: center;">
                        <img id="imageDisplay" src="#" alt="gfdbdfasbdfsb" style="height:auto; width: 100%">
                    </div>
                </div>
                    

                <br>

                <label class="mr-sm-2" for="topicSelect">Select Post Topic</label>
                <div style="margin: 5px 0px">
                    <div class="form-row align-items-center row">
                        <div class="dropdown col-auto">
                            <input type="text" placeholder="Search.." id="topicsearch" class="searchbox form-control" onkeyup="filterFunction('topic')" style="width: 250px;">
                            <input type="hidden" id="hiddentopicsearch" name="hiddentopicsearch">
                            <?php
                            include "db_connection.php";
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
                            $sql = 'SELECT title, topic_ID FROM topics';
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                echo "Connection Error.";
                                exit;
                            }
                            $topicArray = mysqli_fetch_all($result);
                            ?>
                            <div id="topicDropdown" class="dropdown-content" style="width: 250px;">
                                <?php
                                $i = 0;
                                foreach ($topicArray as $topic) {
                                    echo "<li id='topic_li_$i' onmousedown='setSearch(\"topic\", \"topic_li_$i\")'>$topic[0]</li>";
                                    echo "<input type='hidden' id='id_topic_li_$i' value='$topic[1]'>";
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">or</div>
                        <div class="col-auto">
                            <button id="isNewTopic" name="isNewTopic" type="button" class="btn btn-outline-secondary" onclick="toggleNewTopic()" value="0">Create New Topic</button>
                        </div>
                    </div>
                    <input type="text" name="newTopicInput" id="newTopicInput" placeholder="Enter new topic title..." style="display: none; width: 100%;" class="form-control">

                </div>

                
                <div class="form-group row">
                    <div class="col-sm-10">
                        <button id="submitButton" type="submit" class="btn btn-primary">Publish</button>
                    </div>
                </div>
            </form>

            <!-- <div class="list-group col-md-2">
                <h5>Similar posts</h5>
                <a href="./view_ind_post_m.html"
                    class="list-group-item list-group-item-action list-group-item-primary">Example Q1</a>
                <a href="./view_ind_post_m.html"
                    class="list-group-item list-group-item-action list-group-item-secondary">Example Q2</a>
                <a href="./view_ind_post_m.html"
                    class="list-group-item list-group-item-action list-group-item-primary">Example Q3</a>
                <a href="./view_ind_post_m.html"
                    class="list-group-item list-group-item-action list-group-item-secondary">Example Q4</a>
                <a href="./view_ind_post_m.html"
                    class="list-group-item list-group-item-action list-group-item-primary">Example Q5</a>
            </div> -->
        </div>
    </main>

    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"
        style="padding-left: 25px; padding-right: 25px;">
        <p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

        <a href="/"
            class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="./logo.png" alt="mdo" width="200" height="50">
            </svg>
        </a>

        <div class="justify-content-end">
            <p>Phone: 01509 888999</p>
            <p>Email: king@make‐it‐all.co.uk</p>
        </div>
    </footer>




<script>

// $(document).ready(() => {
//     $("form").submit((e) => {
//         e.preventDefault();
//         $.post("create_post.php", {
//             user_ID: <?php echo $_SESSION["user_ID"] ?>,
//             title: $("#title").val(),
//             due_date: $("#due_date").val()
//         }, function(response) {
//             console.log(response);
//             getToDoList();
//             $("#addToDoItem").trigger("reset");
//         });
//     });
// })

    function displayImage(obj) {
        if (obj.files && obj.files[0]) {
            document.getElementById("imageContainer").style.display = "block";
            document.getElementById("imageDisplay").src = URL.createObjectURL(obj.files[0]);
        }
        else {
            document.getElementById("imageDisplay").src = "#";
            document.getElementById("imageContainer").style.display = "none";
        }
            
    }


    function filterFunction(dropdown) {
        document.getElementById(dropdown + 'search').classList.add("is-invalid");
        document.getElementById(dropdown + 'search').classList.remove("is-valid");
        document.getElementById("submitButton").classList.add("disabled");
        document.getElementById('hidden' + dropdown + 'search').value = null;

        var input, filter, ul, li, i;
        input = document.getElementById(dropdown + "search");
        filter = input.value.toUpperCase();
        div = document.getElementById(dropdown + "Dropdown");
        li = div.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            txtValue = li[i].textContent || li[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }

    function setSearch(dropdown, id) {
        document.getElementById('hidden' + dropdown + 'search').value = document.getElementById('id_' + id).value;
        document.getElementById(dropdown + 'search').value = document.getElementById(id).innerHTML;
        document.getElementById(dropdown + 'search').classList.add("is-valid");
        document.getElementById(dropdown + 'search').classList.remove("is-invalid");
        document.getElementById(dropdown + 'Dropdown').classList.remove("show");
        document.getElementById("submitButton").classList.remove("disabled");
    }

    function toggleNewTopic() {
        if (document.getElementById("isNewTopic").value == 0) {
            document.getElementById("newTopicInput").style.display = "block";
            document.getElementById("submitButton").classList.remove("disabled");
            document.getElementById('topicsearch').value = "N/A";
            document.getElementById('topicsearch').disabled = true;;
            document.getElementById("isNewTopic").value = 1;
            document.getElementById("isNewTopic").innerHTML = "Cancel";
        } else {
            document.getElementById("newTopicInput").style.display = "none";
            document.getElementById("submitButton").classList.add("disabled");
            document.getElementById('topicsearch').value = "";
            document.getElementById('topicsearch').disabled = false;
            document.getElementById("isNewTopic").value = 0;
            document.getElementById("isNewTopic").innerHTML = "Create New Topic";
        }


    }
</script>

</body>

</html>