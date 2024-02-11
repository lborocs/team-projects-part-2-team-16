<?php 
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}

if (isset($_POST['asyncINT_ID'])) {
    $INT_ID = $_POST['asyncINT_ID'];
}

function setpic($img_url) {
  if ($img_url == 'null') {
      return "grey.png";
  } else {
      return $img_url;
  }
}

if (isset($_POST['search'])) {
  $searchInput = $_POST['search'];
  $Topic_search = '%' . $searchInput . '%';

  $sql = "SELECT title, content, img_url, post_ID, topic_ID  FROM posts WHERE (topic_ID = $INT_ID) AND (LOWER(title) LIKE '$Topic_search') ORDER BY title ASC";
  $Result = mysqli_query($conn, $sql);
  
  while($resultA = mysqli_fetch_array($Result)){

    $PIC = setpic($resultA["img_url"]);

    echo '
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
        <div class="card mx-auto">
        <img src="' . $PIC . '"
          class="card_img" alt="...">
        <div class="card-body" style="height: 68px; overflow: hidden;">
          <p class="card-text mb-0">' . $resultA["title"] . '</p>
          <a class="stretched-link" href="./view_ind_post_m.php?POST_ID=' . $resultA["post_ID"] . '"></a>
          </div>
          </div>
    </div>';  
  
}

}


elseif (isset($_POST['sortby'])){
  $PHPID = $_POST['sortby'];

switch($PHPID){
  
  case "ABC":
          
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){
      $PIC = setpic($resultA["img_url"]);

    echo '
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
    <div class="card mx-auto">
        <img src="' . $PIC . '"
          class="card_img" alt="...">
        <div class="card-body" style="height: 68px; overflow: hidden;">
          <p class="card-text mb-0">' . $resultA["title"] . '</p>
          <a class="stretched-link" href="./view_ind_post_m.php?POST_ID=' . $resultA["post_ID"] . '"></a>
          </div>
          </div>
    </div>';
    
    }
      
  
  break;
  
  case "Date":
          
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY DATE ASC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){
      $PIC = setpic($resultA["img_url"]);
    
    echo '
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
    <div class="card mx-auto">
        <img src="' . $PIC . '"
          class="card_img" alt="...">
        <div class="card-body" style="height: 68px; overflow: hidden;">
          <p class="card-text mb-0">' . $resultA["title"] . '</p>
          <a class="stretched-link" href="./view_ind_post_m.php?POST_ID=' . $resultA["post_ID"] . '"></a>
          </div>
          </div>
    </div>';
    
    }
        
  
  break;
  
  case "View":
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY views DESC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){
      $PIC = setpic($resultA["img_url"]);
      
    
    echo '
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
    <div class="card mx-auto">
        <img src="' . $PIC . '"
          class="card_img" alt="...">
        <div class="card-body" style="height: 68px; overflow: hidden;">
          <p class="card-text mb-0">' . $resultA["title"] . '</p>
          <a class="stretched-link" href="./view_ind_post_m.php?POST_ID=' . $resultA["post_ID"] . '"></a>
          </div>
          </div>
    </div>';
    
    }
        
  
  break;
  
  default:
          
  $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
  $Result = mysqli_query($conn, $sql);
  
  while($resultA = mysqli_fetch_array($Result)){
    $PIC = setpic($resultA["img_url"]);
  
  echo '
  <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
  <div class="card mx-auto">
      <img src="' . $PIC . '"
        class="card_img" alt="...">
      <div class="card-body" style="height: 68px; overflow: hidden;">
        <p class="card-text mb-0">' . $resultA["title"] . '</p>
        <a class="stretched-link" href="./view_ind_post_m.php?POST_ID=' . $resultA["post_ID"] . '"></a>
        </div>
        </div>
  </div>';
  
  }
      
  }
}

?>

