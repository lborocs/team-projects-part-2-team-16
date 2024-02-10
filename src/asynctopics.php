


<?php 
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}



if (isset($_POST['search'])) {
    $searchInput = $_POST['search'];
    $Topic_search = '%' . $searchInput . '%';

    $sql = "SELECT topic_ID, title, views, posts  FROM topics WHERE LOWER(title) LIKE '$Topic_search' ORDER BY title ASC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
            onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">
            <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';    
        }

}

elseif (isset($_POST['sortby'])){
    $PHPID = $_POST['sortby'];
    

switch($PHPID){
    case "ABC":
        $sql = "SELECT topic_ID, title, views, posts  FROM topics ORDER BY title ASC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
            onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">
            <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';    
        }


        break;

    case "Date":
        $sql = "SELECT topic_ID, title, views, posts  FROM topics ORDER BY Date ASC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';    
        }

        break;

    case "Post":
        $sql = "SELECT topic_ID, title, views, posts  FROM topics ORDER BY posts DESC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';    
        }

        break;

    case "View":
        $sql = "SELECT topic_ID, title, views, posts  FROM topics ORDER BY views DESC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';    
        }

        break;

    default:
        $sql = "SELECT topic_ID, title, views, posts  FROM topics ORDER BY title ASC";
        $Result = mysqli_query($conn, $sql);
    
        while($resultA = mysqli_fetch_array($Result)){

        echo '<div type="button" style="top: 495px;" class="topic1 col-xl"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
                <p>' . $resultA["title"] . '</p>
                <div style="float: right;height: 20px;position: relative;">
                    <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                        <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["posts"] . '
                        <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                    </span>
                </div>
            </div>
        </div>
        <br>';

    } 
    
}
}

?>