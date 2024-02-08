<?php 
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}
if (isset($_POST['asyncINT_ID'])) {
    $INT_ID = $_POST['asyncINT_ID'];
}


if (isset($_POST['sortby']))
{
$PHPID = $_POST['sortby'];

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
