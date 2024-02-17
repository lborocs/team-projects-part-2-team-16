<!DOCTYPE html>
<html lang="en">

<head>
  <title>Posts</title>

  <link rel="stylesheet" type="text/css" href="./posts.css">
  <link rel="icon" type="image/x-icon" href="./logo.ico">

  <style>
    .card_img {
  height: 175px;
  width: 198px;
  border-radius: 3px;
 }
 .title{
  transform: translate(24%);
 }
 .edit{
  max-width: 21%;
  position: absolute;
  border-radius: 3px;
  left: 70%;
  margin-top: -98px;
}
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
    body{
            display: flex;
            flex-direction: column;
        }
        .HeightShown{
            flex: 1;
            min-height: 80vh;
        }
  </style>
  <link href="./headers.css" rel="stylesheet">
</head>

<div class="HeightShown">

<body>

  <?php
include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
      echo "Connection Error." ;
    }

    $Current_Topic = $_GET['Post_topic_ID'];
    $INT_ID = (int)$Current_Topic;

    $sqlTopicName = "SELECT title FROM topics WHERE topic_ID = $INT_ID";
    $resultTN = mysqli_query($conn, $sqlTopicName);
    $TopicArray = mysqli_fetch_assoc($resultTN);
    $TopicTitle = $TopicArray['title'];

    ?>



  <script>
		function settings() {
			window.location.href = "./settings_m.php";
		};
		function logout() {
			window.location.href = "./login.php";
		};
	</script>

<?php 
            session_start();
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
<div class = "title col-xs-6 col-sm-6 col-md-4 my-4" >
  <h1><?php echo "Topic: " . $TopicTitle; ?></h1>
</div>
<div class="input-group mb-3 Search con2">

<input id="IDsearch" type="text" name="IDsearch" class="form-control" placeholder="Enter Post:" aria-label="Text input with dropdown button">
<input type="hidden" name="Post_topic_ID" value="<?php echo $INT_ID; ?>">
<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Sort By</button>

<ul class="dropdown-menu dropdown-menu-end">
<li>
    <button name="ABC" class="dropdown-item" type="button" onclick="change('ABC')">Alphabetically</button>
</li>
<li>
    <button name="Date" class="dropdown-item" type="button" onclick="change('Date')">Date Posted</button>
</li>
<li>
    <button name="View" class="dropdown-item" type="button" onclick="change('View')">Views</button>
</li>
</ul>
</div>

<?php
if($_SESSION["role"] == "Manager"){
?>

<form>

<button class="btn btn-primary input-group edit dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Edit</button>

<ul class="dropdown-menu dropdown-menu-end">
<li>
    <button class="dropdown-item" type="button"  onclick="window.location.href='./create_post.php';">Create Post</button>
</li>
<li>
    <button class="dropdown-item" type="button" onclick="deletetopic()">Delete Topic</button>
</li>
</ul>
</form>

<form id="TopicDeletion" method="post" action="deletetopic.php">
  <input type="hidden" name="deleteID" value="<?php echo $INT_ID; ?>">
</form>

<script>

function deletetopic() {

  if (confirm('Are you sure you want to delete this topic?')) {
    document.getElementById("TopicDeletion").submit();
} else {
    return;
} 
}

</script>

<?php
} else{
?>

<button type="button" class="btn btn-primary input-group mb-3 createTopic conButton"
    onclick="window.location.href='./create_post.php';">Create Post</button>

<?php
}
?>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>

function AsyncSearch() {
        var searchInput = $("#IDsearch").val();

        if (searchInput.trim().length > -1) {
            $.ajax({
                type: "POST",
                url: "asyncposts.php",
                data: {
                    search: searchInput,
                    asyncINT_ID: "<?php echo $INT_ID; ?>" ,
                    asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"},
                success: function (response) {
                    $("#async").find(".this-div").html(response);
                },
                error: function (e) {
                    console.error('Error');
                }
            });
        }
    }

    $("#IDsearch").on('input', AsyncSearch);

function change(sortby)
     {
        $.ajax({
            type: "POST",
            url: "asyncposts.php",
            data:
             {   sortby: sortby,
                 asyncINT_ID: "<?php echo $INT_ID; ?>" ,
                 asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"},

            success:
             function (response){
                $("#async").find(".this-div").html(response); 

             },
            error:
             function (e){                  
                console.error('Error');
            }

        });
    }

</script>

  <div id="async" class="container con1">
  <div class="row mx-auto this-div">

<?php

if (isset($Current_Topic)) {
  $sql = "SELECT views FROM topics WHERE topic_ID = $INT_ID";
  $result = mysqli_query($conn, $sql);

          $changingview = mysqli_fetch_assoc($result);
          $currentViews = $changingview['views'];

          $newViews = $currentViews + 1;

          $updateSql = "UPDATE topics SET views = $newViews WHERE topic_ID = $INT_ID";

          if (mysqli_query($conn, $updateSql)) {

          } else {
              echo "Error";
          }
  
}

        
$sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
$Result = mysqli_query($conn, $sql);

while($resultA = mysqli_fetch_array($Result)){

if ($resultA["img_url"] == 'null') {
    $PIC = "grey.png";
} else {
    $PIC = $resultA["img_url"];
}

echo '
<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
<div class="card mx-auto">
    <img src="' . $PIC . '"
      class="card_img" alt="...">
    <div class="card-body" style="height: 68px; overflow: hidden;">
      <p class="card-text mb-0">' . $resultA["title"] . '</p>
      <a class="stretched-link" href="./get_ind_post.php?POST_ID=' . $resultA["post_ID"] . '"></a>
      </div>
      </div>
</div>';

}
    

?>
  </div>
  </div>
</div>
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
            <p>Email: king@make-it-all.co.uk</p>
        </div>
    </footer>

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
                    $(this).addClass("text-light bg-dark border-light");
                }
            });
        }
    })
</script>
</body>