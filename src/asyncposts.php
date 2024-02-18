<?php 
// Obtain the light/dark mode choice
if (isset($_POST['asyncCOLOR'])) {
  $COLOR = $_POST['asyncCOLOR'];
}

// PostList displays the posts in the topic they get as cards contianing the photo and post title.
function PostList($resultA) {

  $PIC = setpic($resultA["img_url"]);

  return '<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ">
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

//Connect to Database
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}

// Obtain topic ID on the page to handle the async
if (isset($_POST['asyncINT_ID'])) {
    $INT_ID = $_POST['asyncINT_ID'];
}

// If no photo was selected then a neutral user icon is displayed
function setpic($img_url) {
  if ($img_url == 'null') {
      return "grey.png";
  } else {
      return $img_url;
  }
}

// Displays posts avalible asynchronusly when using the search bar
if (isset($_POST['search'])) {
  $searchInput = $_POST['search'];
  $Topic_search = '%' . $searchInput . '%';

  $sql = "SELECT title, content, img_url, post_ID, topic_ID  FROM posts WHERE (topic_ID = $INT_ID) AND (LOWER(title) LIKE '$Topic_search') ORDER BY title ASC";
  $Result = mysqli_query($conn, $sql);
  
  if(mysqli_num_rows($Result) > 0){
    while($resultA = mysqli_fetch_array($Result)){
      echo PostList($resultA);
    }
    } else {

      echo "<div style='text-align: center;'>";
      echo "Sorry no available results for: " . $searchInput;
      echo "</div>";

    }

}

// Obtains the name of the button pressed in sort by dropdown
elseif (isset($_POST['sortby'])){
  $PHPID = $_POST['sortby'];

// A switch case is used to display the posts avalible in which ever order selected.
switch($PHPID){
  
  case "ABC":
          
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){

      echo PostList($resultA);    

    
    }
      
  
  break;
  
  case "Date":
          
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY DATE ASC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){
    
      echo PostList($resultA);    

    
    }
        
  
  break;
  
  case "View":
    $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY views DESC";
    $Result = mysqli_query($conn, $sql);
    
    while($resultA = mysqli_fetch_array($Result)){      
    
      echo PostList($resultA);    

    
    }
        
  
  break;
  
  default:
          
  $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
  $Result = mysqli_query($conn, $sql);
  
  while($resultA = mysqli_fetch_array($Result)){
  
    echo PostList($resultA);    

  
  }
      
  }
}

?>

<!-- Applies dark mode classes to nessasery elements if dark mode is enabled -->
<script>
    <?php
    if($COLOR == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}
    ?>
    
    $(document).ready(function() {
        if ("<?php echo $colour ?>" == "text-light bg-dark") {
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false && $(this).parents("header").length == 0) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        }
    })
</script>