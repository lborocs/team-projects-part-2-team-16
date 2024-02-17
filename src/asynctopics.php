<?php 

if (isset($_POST['asyncCOLOR'])) {
    $COLOR = $_POST['asyncCOLOR'];
}

function TopicList($resultA) {

    return '<div type="button" style="top: 495px; overflow: hidden;" class="topic1 col-xl lightB"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
            <p>' . $resultA["title"] . '</p>
            <div style="height: 20px;position: relative;">
                <span style="display: inline-block; font-size: 17px;position: absolute; left: -70px;width: 220px;bottom: 15px;">
                    Project
                </span>
                <span style="display: inline-block; font-size: 17px;position: absolute;right: 0px;width: 220px;bottom: 15px;">
                    <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["COUNT(post.topic_ID)"] . '
                    <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                </span>
            </div>
        </div>
        </div>
        <br>';

}

function NonProject($resultA) {
    return '<div type="button" style="top: 495px; overflow: hidden;" class="topic1 col-xl lightB"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
            <p>' . $resultA["title"] . '</p>
            <div style="float: right;height: 20px;position: relative;">
                <span style="font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                    <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["COUNT(post.topic_ID)"] . '
                    <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                </span>
            </div>
        </div>
        </div>
        <br>';
}

function TopicChecker($resultA, $conn) {
    if (isset($_POST['asyncUSER_ID'])) {
        $USERS_ID = $_POST['asyncUSER_ID'];
    }
    if (isset($_POST['asyncROLE_ID'])) {
        $ROLL_ID = $_POST['asyncROLE_ID'];
    }
    if (($resultA['project_ID'] !== null)){
        $prjTEST = $resultA['project_ID'];
        $sqlProjectCheck = "SELECT project_ID FROM project";
        $resultProjectCheck = mysqli_query($conn, $sqlProjectCheck);
        while($ProjectID_LIST = mysqli_fetch_assoc($resultProjectCheck)){
            if (($ProjectID_LIST['project_ID'] == $prjTEST)) {
                if(($ROLL_ID == "Manager")){
                    echo TopicList($resultA);
                } 
                else {
                $sqlProject = "SELECT user_ID FROM tasks WHERE project_id = $prjTEST";
                $resultProject = mysqli_query($conn, $sqlProject);
                    while($UserID_LIST = mysqli_fetch_assoc($resultProject)){
                        if($ROLL_ID == "TL"){
                            $sqlTL = "SELECT team_leader FROM project WHERE project_ID = $prjTEST";
                            $resultTL = mysqli_query($conn, $sqlTL);
                            while($TLID_LIST = mysqli_fetch_assoc($resultTL)){
                                if($USERS_ID == $TLID_LIST["team_leader"]){
                                    echo TopicList($resultA);
                                    break 2;
                                } 
                            }
                        }else{
                        if (($UserID_LIST['user_ID'] == $USERS_ID)) {
                             echo TopicList($resultA);
                                break;
                        }
                    }
            }
        }
                
            }
        }
        
    } else {
        echo NonProject($resultA);
    }
}

include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}



if (isset($_POST['search'])) {
    $searchInput = $_POST['search'];
    $Topic_search = '%' . $searchInput . '%';

    $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID WHERE LOWER(topic.title) LIKE '$Topic_search' GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.title";
    $Result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($Result) > 0){
        while($resultA = mysqli_fetch_array($Result)){
            TopicChecker($resultA, $conn);
        }
    } else {
        echo "Sorry no avalible results for: " . $searchInput;
    }
    
}

elseif (isset($_POST['sortby'])){
    $PHPID = $_POST['sortby'];
    

switch($PHPID){
    case "ABC":
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.title";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
            TopicChecker($resultA, $conn);    
        }


        break;

    case "Date":
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.date ASC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
            TopicChecker($resultA, $conn);    
        }

        break;

    case "Post":
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY COUNT(post.topic_ID) DESC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
            TopicChecker($resultA, $conn);     
        }

        break;

    case "View":
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.views DESC";
        $Result = mysqli_query($conn, $sql);
        
        while($resultA = mysqli_fetch_array($Result)){
    
            TopicChecker($resultA, $conn);     
        }

        break;

    case "Project":
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID WHERE topic.project_ID IS NOT NULL GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.project_ID";
        $Result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($Result) > 0){
            while($resultA = mysqli_fetch_array($Result)){
                TopicChecker($resultA, $conn);
            }
        } else {
            echo "Sorry no avalible results";
        }
    
        break;

    default:
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.title";
        $Result = mysqli_query($conn, $sql);
    
        while($resultA = mysqli_fetch_array($Result)){

            TopicChecker($resultA, $conn);

    } 
    
}
}

?>
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
            $(".lightB").addClass("border-light");
        }
    })
</script>