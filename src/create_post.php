<?php 
session_start();

// validates input and creates the new post
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
            echo "<script type='text/javascript'>alert('title.');</script>";
            return false;
        }
    } else {
        return false;
    }
    if (isset($_POST["postBody"])) {
        $body = $_POST["postBody"];
        if (strlen($body) > 1000) {
            echo "<script type='text/javascript'>alert('body.');</script>";
            return false;
        }
    } else {
        return false;
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
                $create_topic_stmt = $conn->prepare("INSERT INTO topics (topic_ID, title, Date, views) VALUES (:topicID, :title, DATE :date, 1)");
                $create_topic_stmt->bindParam(':topicID', $topicID, PDO::PARAM_INT);
                $create_topic_stmt->bindParam(':title', $newTopic, PDO::PARAM_STR);
                $create_topic_stmt->bindParam(':date', $date, PDO::PARAM_STR);

                if (!$create_topic_stmt->execute()) {
                    return false;
                }
            }
        } else if (isset($_POST["hiddentopicsearch"])){
            if (is_numeric($_POST["hiddentopicsearch"])) {
                $topicID = $_POST["hiddentopicsearch"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }  else {
        return false;
    }   

    $result = $conn->query("SELECT max(post_ID) FROM posts");
    $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
    if ($maxID == null) {
        $postID = 1;
    } else {
        $postID = $maxID[0] + 1;
    }
    
    $imageUploaded = false;
    if (isset($_FILES["imageInput"])){
        try {
            $file = $_FILES["imageInput"];
            $fileName = $file["name"];
            $fileTmpName = $file["tmp_name"];
            $fileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
            $fileHash = uniqid();
            if (!isset($_FILES['imageInput']['error']) || is_array($_FILES['imageInput']['error'])) {
                echo "<script type='text/javascript'>alert('Error within image file, post created without image.');</script>";
                throw new RuntimeException("error within image", 1);
            }
            if (false === array_search($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script type='text/javascript'>alert('Image file too large, post created without image.');</script>";
                throw new RuntimeException("file foramt not accepted", 2);
            }
            if ($file['size'] > 500000) {
                echo "<script type='text/javascript'>alert('Image file too large, post created without image.');</script>";
                throw new RuntimeException("image too large", 3);
            }
            $destination = "postImageUploads/$fileHash.$fileType";
            // $destination = "postImageUploads/" . $fileName;
            if (move_uploaded_file($fileTmpName, $destination)) {
                echo "file uploaded";
                $imageUploaded = true;
            } else {
                echo "file upload failed";
            }
        } catch (RuntimeException $e) {
            echo "<script type='text/javascript'>alert('Issue with image file, post created without image.');</script>";
            $destination = "null";
        }
        
    } else {
        $destination = "null";
    }

    $create_post_stmt = $conn->prepare("INSERT INTO posts (post_ID, user_ID, topic_ID, title, content, img_url, Date, views) VALUES (:post_ID, :user_ID, :topic_ID, :title, :content, :img_url, DATE :date, 1)");
    $create_post_stmt->bindParam(":post_ID", $postID, PDO::PARAM_INT);
    $create_post_stmt->bindParam(":user_ID", $_SESSION['user_ID'], PDO::PARAM_INT);
    $create_post_stmt->bindParam(":topic_ID", $topicID, PDO::PARAM_INT);
    $create_post_stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $create_post_stmt->bindParam(":content", $body, PDO::PARAM_STR);
    $create_post_stmt->bindParam(":img_url", $destination, PDO::PARAM_STR);
    $create_post_stmt->bindParam(":date", $date, PDO::PARAM_STR);

    if (!$create_post_stmt->execute()) {
        return false;
    } else {
        header("location: get_ind_post.php?POST_ID=$postID");
        return true;
    }
}

// gets the post the user wants to edit from the DB
function get_edit_post() {
    try {
        include "db_connection.php";
        $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
    } catch (PDOException $e) {
        echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
        return false;
    }

    $getPostQuery = $conn->prepare("SELECT * FROM posts where post_ID = :post_ID");
    $getPostQuery->bindParam(":post_ID", $_GET["edit_ID"], PDO::PARAM_INT);
    if(!$getPostQuery->execute()) {
        header("location:  ");
    }
    return $getPostQuery->fetch(PDO::FETCH_ASSOC);
}

// validates and then commits changes to the post
function editPost() {
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
            echo "<script type='text/javascript'>alert('title.');</script>";
            return false;
        }
    } else {
        return false;
    }
    if (isset($_POST["postBody"])) {
        $body = $_POST["postBody"];
        if (strlen($body) > 1000) {
            echo "<script type='text/javascript'>alert('body.');</script>";
            return false;
        }
    } else {
        return false;
    }

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
                $create_topic_stmt = $conn->prepare("INSERT INTO topics (topic_ID, title, Date, views, posts) VALUES (:topicID, :title, DATE :date, 1, 1)");
                $create_topic_stmt->bindParam(':topicID', $topicID, PDO::PARAM_INT);
                $create_topic_stmt->bindParam(':title', $newTopic, PDO::PARAM_STR);
                $create_topic_stmt->bindParam(':date', $date, PDO::PARAM_STR);

                if (!$create_topic_stmt->execute()) {
                    return false;
                }
            }
        } else if (isset($_POST["hiddentopicsearch"])){
            if (is_numeric($_POST["hiddentopicsearch"])) {
                $topicID = intval($_POST["hiddentopicsearch"]);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }  else {
        return false;
    }

    $imageUploaded = false;
    if (isset($_FILES["imageInput"]) && $_POST['imageChanged'] == 1){
        try {
            $file = $_FILES["imageInput"];
            $fileName = $file["name"];
            $fileTmpName = $file["tmp_name"];
            $fileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
            $fileHash = uniqid();
            if (!isset($_FILES['imageInput']['error']) || is_array($_FILES['imageInput']['error'])) {
                echo "<script type='text/javascript'>alert('Error within image file, post created without image.');</script>";
                throw new RuntimeException("error within image", 1);
            }
            if (false === array_search($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script type='text/javascript'>alert('Image file too large, post created without image.');</script>";
                throw new RuntimeException("file foramt not accepted", 2);
            }
            if ($file['size'] > 500000) {
                echo "<script type='text/javascript'>alert('Image file too large, post created without image.');</script>";
                throw new RuntimeException("image too large", 3);
            }
            $destination = "postImageUploads/$fileHash.$fileType";
            // $destination = "postImageUploads/" . $fileName;
            if (move_uploaded_file($fileTmpName, $destination)) {
                echo "file uploaded";
                $imageUploaded = true;
            } else {
                echo "file upload failed";
            }
        } catch (RuntimeException $e) {
            echo "<script type='text/javascript'>alert('Issue with image file, post created without image.');</script>";
        }
        
    } else {
    }
    $postID = intval($_POST["edit_ID"]); 

    if ($imageUploaded) {
        $edit_post_stmt = $conn->prepare("UPDATE posts SET topic_ID = :topic_ID, title = :title, content = :content, img_url = :img_url where post_ID = :post_ID");
        $edit_post_stmt->bindParam(":img_url", $destination, PDO::PARAM_STR);
    } else {
        // $edit_post_stmt = $conn->prepare("UPDATE posts SET topic_ID = :topic_ID, title = :title, content = :content where post_ID = :postID");
        $edit_post_stmt = $conn->prepare("UPDATE posts SET topic_ID = :topic_ID, title = :title, content = :content where post_ID = :post_ID");
    }


    $edit_post_stmt->bindParam(":post_ID", $postID, PDO::PARAM_INT);
    $edit_post_stmt->bindParam(":topic_ID", $topicID, PDO::PARAM_INT);
    $edit_post_stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $edit_post_stmt->bindParam(":content", $body, PDO::PARAM_STR);
    

    if (!$edit_post_stmt->execute()) {
        return false;
    } else {
        header("location: get_ind_post.php?POST_ID=$postID");
        return true;
    }
}

if (isset($_POST["submitButton"])) {
    if (!createPost()) {
        echo "<script type='text/javascript'>alert('Failed to create the post, invalid data entered.');</script>";        
    }
} else if (isset($_POST["confirmEditButton"])) {
    if (!editPost()) {
        echo "<script type='text/javascript'>alert('Failed to create the post, invalid data entered.');</script>";        
    }
} else if (isset($_GET["edit_ID"])) {
    $editingPost = get_edit_post();
    $editPostTitle = $editingPost["title"];
    $editPostcontent = $editingPost["content"];
    $editPostUrl = $editingPost["img_url"];
    $editPostTopic = $editingPost["topic_ID"];
} 

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if (isset($editingTask)) {echo "Edit";} else {echo "Create";}?> Post</title>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

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

<!-- applies dark mode classes to all elements if dark mode is enabled -->
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
    <!-- loads the header -->
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
        
        <h1 class="my-5"><?php if (isset($editingPost)) {echo "Edit Post";} else {echo "Create Post";}?></h1>
        <div class="d-flex flex-wrap">
            <form class="col-md-8" autocomplete="off" method="post" action="#" enctype="multipart/form-data">
                <!-- title input -->
                <div class="form-group row">
                    <label for="postTitle" class="col-auto-2 col-form-label" style="margin-left: 0px; margin-right: 0px;">Title</label>
                    <div class="col-auto-10">
                        <input type="text" class="form-control" id="postTitle" name="postTitle" placeholder="..." required
                        value="<?php if (isset($editingPost)) {echo $editPostTitle;}?>">
                    </div>
                </div>

                <!-- post body input -->
                <div class="form-group row" style="margin-left: 0px; margin-right: 0px;">
                    <label for="postBody" style="padding-left: 0px;">Body</label>
                    <textarea name="postBody" class="form-control" id="postBody" rows="10" placeholder="..." required><?php if (isset($editingPost)) {echo $editPostcontent;} else {echo null;} ?></textarea>
                </div>

                <br>

                <!-- file select input for including an image -->
                <div class="form-group">
                    <label for="imageInput">Include a picture? (Optional)</label>
                    <input name="imageInput" type="file" class="form-control" id="imageInput" accept=".png,.jpg,.jpeg,.ico" onchange=displayImage(this)>
                    <input type="hidden" name="imageChanged" id="imageChanged" value=0>
                </div>

                <!-- image preview, if a file has been selected -->
                <div id="imageContainer" style="margin: 10px auto; display: <?php if (isset($editingPost) && $editingPost["img_url"] != "null") {echo "block";} else {echo "none";} ?>; width: 50%">
                    <p style="margin: 5px;"><b><u>Image Preview</u></b></p>
                    <div style="border: 10px solid #797676; border-radius: 17px; text-align: center;">
                        <img id="imageDisplay" alt="failed to display image" style="height:auto; width: 100%"
                            src="<?php if (isset($editingPost)) {echo $editPostUrl;} else {echo "#";} ?>">
                    </div>
                </div>

                <br>

                <!-- selecting a topic the post is related to -->
                <label class="mr-sm-2" for="topicSelect">Select Post Topic</label>
                <div style="margin: 5px 0px">
                    <div class="form-row align-items-center row">
                        <div class="dropdown col-auto">
                            <input type="text" placeholder="Search.." id="topicsearch" class="searchbox form-control" onkeyup="filterFunction('topic')" style="width: 250px;">
                            <input type="hidden" id="hiddentopicsearch" name="hiddentopicsearch">
                            <?php
                            include "db_connection.php";
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
                            if ($_SESSION['role'] != "Manager") {
                                $user_ID = $_SESSION['user_ID'];
                                $sql = "select * from topics where project_ID is null or project_ID in (select project_ID from tasks where user_ID = $user_ID) or project_ID in (select project_ID from project where team_leader = $user_ID);";
                            } else {
                                $sql = "select * from topics;";
                            }
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                echo "Connection Error.";
                                exit;
                            }
                            $topicArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
                            ?>
                            <div id="topicDropdown" class="dropdown-content" style="width: 250px;">
                                <?php
                                $i = 0;
                                foreach ($topicArray as $topic) {
                                    echo "<li id='topic_li_$i' onmousedown='setSearch(\"topic\", \"topic_li_$i\")'>".$topic['title']."</li>";
                                    echo "<input type='hidden' id='id_topic_li_$i' value='".$topic["topic_ID"]."'>";
                                    if (isset($editingPost)) {
                                        if ($editingPost["topic_ID"] == $topic["topic_ID"]) {
                                            $setTopicTo = $i;
                                        }
                                    } else if (isset($_GET['topic_ID'])) {
                                        if ($_GET["topic_ID"] == $topic["topic_ID"]) {
                                            $setTopicTo = $i;
                                        }
                                    }
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>
                        <!-- the toggle button for creating a new topic, toggles the text input below it -->
                        <div class="col-auto">or</div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleNewTopic()">Create New Topic</button>
                            <input type="hidden" name="isNewTopic" id="isNewTopic" value="0">
                        </div>
                    </div>
                    <input type="text" name="newTopicInput" id="newTopicInput" placeholder="Enter new topic title..." style="display: none; width: 100%;" class="form-control">

                </div>

                <!-- submit buttons -->
                <div class="form-group row">
                    <div class="col-sm-10">
                        <?php
                        if (!isset($editingPost)) {
                            echo '<button id="submitButton" name="submitButton" type="submit" class="btn btn-primary disabled">Publish</button>';
                        } else {
                            echo "<input type='hidden' name='edit_ID' value=$_GET[edit_ID]>";
                            echo '<button id="submitButton" name="confirmEditButton" type="submit" class="btn btn-primary">Confirm Edit</button>';
                        }
                        ?>
                    </div>
                </div>
            </form>

            
        </div>
    </main>

    <?php include "footer.php"; ?>


</body>

</html>

<script>

    function displayImage(obj) {
        document.getElementById("imageChanged").value = 1;
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
        if (document.getElementById('id_' + id)) {
            document.getElementById('hidden' + dropdown + 'search').value = document.getElementById('id_' + id).value;
            document.getElementById(dropdown + 'search').value = document.getElementById(id).innerHTML;
            document.getElementById(dropdown + 'search').classList.add("is-valid");
            document.getElementById(dropdown + 'search').classList.remove("is-invalid");
            document.getElementById(dropdown + 'Dropdown').classList.remove("show");
            document.getElementById("submitButton").classList.remove("disabled");
        }
    }

    function toggleNewTopic() {
        if (document.getElementById("isNewTopic").value == 0) {
            document.getElementById("newTopicInput").style.display = "block";
            document.getElementById("submitButton").classList.remove("disabled");
            document.getElementById('topicsearch').disabled = true;
            document.getElementById("isNewTopic").value = 1;
            document.getElementById("isNewTopic").innerHTML = "Cancel";
        } else {
            document.getElementById("newTopicInput").style.display = "none";
            document.getElementById("submitButton").classList.add("disabled");
            document.getElementById('topicsearch').disabled = false;
            document.getElementById("isNewTopic").value = 0;
            document.getElementById("isNewTopic").innerHTML = "Create New Topic";
        }
    }

    <?php if(isset($editingPost)) {echo "setSearch('topic', 'topic_li_$setTopicTo')";}?>
    <?php if(isset($_GET["topic_ID"])) {echo "setSearch('topic', 'topic_li_$setTopicTo')";} ?>
    
</script>
