<!DOCTYPE html>
<html lang="en">

<head>
  <title>Posts</title>

  <link rel="stylesheet" type="text/css" href="./posts.css">


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
  <link href="./headers.css" rel="stylesheet">
</head>

<body>

  <?php
include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
      echo "Connection Error." ;
    }
/*
    setcookie('TopicID', $_GET['Post_topic_ID']);
    session_start();
    $_SESSION['TopicID'] = $_GET['Post_topic_ID'];
    if (isset($_COOKIE['TopicID'])) {
      $Current_Topic = $_COOKIE['TopicID'];
  } else {
      echo "Cookie 'TopicID' not set <br>";
  }
  */
  $Current_Topic = $_GET['Post_topic_ID'];
  
    
    $INT_ID = (int)$Current_Topic;
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
				//include "./dashboard_m.php";
			}else if($_SESSION["role"] == "TL"){
				$topicview = "link-dark";
				$taskcreate = "link-dark";
				$taskview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_tl.php";
				//include "./view_team_tl.php";
			}else if($_SESSION["role"] == "Employee"){
				$topicview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_e.php";
				//include "./dashboard_e.php";
			}
		?>

<div class="input-group mb-3 Search con2">

<input id="IDsearch" type="text" name="Search" class="form-control" placeholder="Enter Post:" aria-label="Text input with dropdown button">
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

  <button type="button" class="btn btn-primary input-group mb-3 createTopic conButton"
    onclick="window.location.href='./create_post.php';">Create Post</button>




    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>

$(document).ready(function() {
     console.log("Hello");
});

function change(sortby)
     {
        var searchinput = $("#IDsearch").val();

        console.log('searchInputValue:',searchinput);
        console.log('sortby:', sortby);

        $.ajax({
            type: "POST",
            url: "asyncposts.php",
            data:
             {   sortby: sortby,
                 search: searchinput,
                 asyncINT_ID: "<?php echo $INT_ID; ?>" },

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


        
$sql = "SELECT title, content, img_url  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
$Result = mysqli_query($conn, $sql);

while($resultA = mysqli_fetch_array($Result)){

echo '
<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
<div class="card mx-auto">
    <img src="https://thumbs.dreamstime.com/b/software-engineer-portrait-smiling-young-vietnamese-69422682.jpg"
      class="card_img" alt="...">
    <div class="card-body">
      <p class="card-text mb-0">' . $resultA["title"] . $resultA["content"] . '</p>
      <a class="stretched-link" href="./view_ind_post_m.php"></a>
      </div>
      </div>
</div>';

}
    


?>
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

</body>
