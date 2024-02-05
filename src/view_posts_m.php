<!DOCTYPE html>
<html lang="en">

<head>
  <title>Posts</title>

  <link rel="stylesheet" type="text/css" href="./posts.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link href="/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Managers Dash</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">

  <link href="/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <link rel="apple-touch-icon" href="/docs/5.0/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
  <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
  <link rel="manifest" href="/docs/5.0/assets/img/favicons/manifest.json">
  <link rel="mask-icon" href="/docs/5.0/assets/img/favicons/safari-pinned-tab.svg" color="#7952b3">
  <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon.ico">
  <meta name="theme-color" content="#7952b3">


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

    setcookie('TopicID', $_GET['Post_topic_ID']);
    session_start();
    $_SESSION['TopicID'] = $_GET['Post_topic_ID'];
    if (isset($_COOKIE['TopicID'])) {
      $Current_Topic = $_COOKIE['TopicID'];
  } else {
      echo "Cookie 'TopicID' not set <br>";
  }
    //$Current_Topic = $_GET['Post_topic_ID'];
  
    
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
	<header class="p-3 mb-3 border-bottom">
		<div class="container">
			<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">

				<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
					<li><a href="./dashboard_m.php" class="nav-link px-2 link-dark">Dashboard</a></li>
					<li><a href="./view_topics_m.php" class="nav-link px-2 link-dark">Topics</a></li>
					<li><a href="./create_task_m.php" class="nav-link px-2 link-dark">Assign Tasks</a></li>
				</ul>

				<form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" action="./view_topics_m.php">
					<input type="search" class="form-control" placeholder="Search Topics" aria-label="Search">
				</form>

				<div class="dropdown text-end">
					<a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
						data-bs-toggle="dropdown" aria-expanded="false">
						<img src="./icon.png" alt="mdo" width="32" height="32" class="rounded-circle">
					</a>
					<ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
						<li><a class="dropdown-item" href="./create_topic_m.php">Create New Topic...</a></li>
						<li><a class="dropdown-item" href="./manageEmp.php">Manage Employees</a></li>
						<li><a class="dropdown-item" href="#" onclick="settings()">Settings</a></li>
						<li>
							<hr class="dropdown-divider">
						</li>
						<li><a class="dropdown-item" href="#" onclick="logout()">Sign out</a></li>
					</ul>
				</div>
			</div>
		</div>
	</header>

  <form action="view_posts_m.php" method="post">
    <div class="input-group mb-3 Search con2">
        <input type="text" name="Search" class="form-control" placeholder="Enter Post:" aria-label="Text input with dropdown button">
        <input type="hidden" name="Post_topic_ID" value="<?php echo $INT_ID; ?>">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false">Sort By</button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><button name="ABC" class="dropdown-item" type="submit">Alphabetically</button></li>
            <li><button name="Date" class="dropdown-item" type="submit">Date Posted</button></li>
            <li><button name="View" class="dropdown-item" type="submit">Views</button></li>
        </ul>
        
    </div>
    </form>

  <button type="button" class="btn btn-primary input-group mb-3 createTopic conButton"
    onclick="window.location.href='./create_post_m.php';">Create Post</button>

  <div class="container con1">
  <div class="row mx-auto">





<?php



if (isset($_POST['Search']) && $_POST['Search'] !== ""){
  $INT_ID = $_POST['Post_topic_ID'];
  $PHPID = "Search";
  $Topic_search = "%" . strtolower($_POST['Search']) . "%";
}
else{
if (isset($_POST['ABC'])){
  $INT_ID = $_POST['Post_topic_ID'];
  $PHPID = "ABC";
}
elseif (isset($_POST['Date'])){
  $INT_ID = $_POST['Post_topic_ID'];
  var_dump($_POST);
  $PHPID = "Date";
}
elseif(isset($_POST['View'])){
  $INT_ID = $_POST['Post_topic_ID'];
  $PHPID = "View";
}
}

switch($PHPID){

  case "Search":
    $sql = "SELECT title, content, img_url  FROM posts WHERE LOWER(title) LIKE '$Topic_search' ORDER BY title ASC";
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

    break;

case "ABC":
        
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
    

break;

case "Date":
        
  $sql = "SELECT title, content, img_url  FROM posts WHERE topic_ID = $INT_ID ORDER BY DATE ASC";
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
      

break;

case "View":
  $sql = "SELECT title, content, img_url  FROM posts WHERE topic_ID = $INT_ID ORDER BY views DESC";
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
      

break;

default:
        
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
    
}

?>
  </div>

  <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"
    style="padding-left: 25px; padding-right: 25px;">
    <p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

    <a href="/"
      class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
      <img src="complogo.png" alt="mdo" width="200" height="50">
      </svg>
    </a>

    <div class="justify-content-end">
      <p>Phone: 01509 888999</p>
      <p>Email: king@make‐it‐all.co.uk</p>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

</body>
