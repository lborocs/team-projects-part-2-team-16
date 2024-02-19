<?php 
// Obtain the light/dark mode choice
if (isset($_POST['asyncCOLOR'])) {
    $COLOR = $_POST['asyncCOLOR'];
}

/* The two following functions display the individual topics on the page as buttons. They both dispaly the title of the topic,
the views each topic has and how many posts are on that topic */

// TopicList is used if the topic is a confidential topic relating to a project and includes 'Project' in the bottom left corner to differentiate
function TopicList($resultA) {

    return '<div type="button" style="top: 495px; overflow: hidden;" class="topic1 col-xl lightB"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
            <p>' . $resultA["title"] . '</p>
            <div style="height: 20px;position: relative;">
                <span style="display: inline-block; font-size: 17px;position: absolute; left: -70px;width: 220px;bottom: 15px;">
                    Project
                </span>
                <span style="display: inline-block; font-size: 17px;position: absolute;right: 5px;width: 220px;bottom: 15px;">
                    <img src="posts-icon.png" alt="" style="height: 20px; width: 20px;"> posts: ' . $resultA["COUNT(post.topic_ID)"] . '
                    <img src="view-icon.png" alt="" style="height: 20px; width: 20px;margin-left: 15px;"> views: ' . $resultA["views"] . '
                </span>
            </div>
        </div>
        </div>
        <br>';

}

// NonProject is used if it is a normal topic that anyone can see
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

// TopicChecker is used to display the topics only avalible for the user signed in
function TopicChecker($resultA, $conn) {
    // Obtain user ID and user role
    if (isset($_POST['asyncUSER_ID'])) {
        $USERS_ID = $_POST['asyncUSER_ID'];
    }
    if (isset($_POST['asyncROLE_ID'])) {
        $ROLL_ID = $_POST['asyncROLE_ID'];
    }
    if (($resultA['project_ID'] !== null)){
        // Checks to see if the topic has a project ID
        $prjTEST = $resultA['project_ID'];
        $sqlProjectCheck = "SELECT project_ID FROM project";
        $resultProjectCheck = mysqli_query($conn, $sqlProjectCheck);
        while($ProjectID_LIST = mysqli_fetch_assoc($resultProjectCheck)){
            if (($ProjectID_LIST['project_ID'] == $prjTEST)) {
                // Managers will be able to view every topic, including all related to projects
                if(($ROLL_ID == "Manager")){
                    echo TopicList($resultA);
                } 
                else {
                // Checks to see the users working on projects
                $sqlProject = "SELECT user_ID FROM tasks WHERE project_id = $prjTEST";
                $resultProject = mysqli_query($conn, $sqlProject);
                    while($UserID_LIST = mysqli_fetch_assoc($resultProject)){
                        if($ROLL_ID == "TL"){
                            // Selects the team leader in charge of the project in question
                            $sqlTL = "SELECT team_leader FROM project WHERE project_ID = $prjTEST";
                            $resultTL = mysqli_query($conn, $sqlTL);
                            while($TLID_LIST = mysqli_fetch_assoc($resultTL)){
                                // If they are the team leader in charge then they will be able to view that topic relating to the project
                                if($USERS_ID == $TLID_LIST["team_leader"]){
                                    echo TopicList($resultA);
                                    break 2;
                                } 
                            }
                        }else{
                        // If they are employee on the project then they will be able to view that topic relating to the project
                        if (($UserID_LIST['user_ID'] == $USERS_ID)) {
                             echo TopicList($resultA);
                                break;
                        }
                    }
            }
        }
                
            }
        }
    // If it is not a project topic, therefore not confidentual then everyone can view it   
    } else {
        echo NonProject($resultA);
    }
}

//Connect to Database
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}


// Displays topics avalible asynchronusly when using the search bar
if (isset($_POST['search'])) {
    $searchInput = $_POST['search'];
    $Topic_search = '%' . $searchInput . '%';

    $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID WHERE LOWER(topic.title) LIKE ? GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.title";
    $Result = mysqli_execute_query($conn, $sql, array($Topic_search));
    
    if(mysqli_num_rows($Result) > 0){
        while($resultA = mysqli_fetch_array($Result)){
            TopicChecker($resultA, $conn);
        }
    } else {
        echo "Sorry no available results for: " . $searchInput;
    }
    
}

// Obtains the name of the button pressed in sort by dropdown
elseif (isset($_POST['sortby'])){
    $PHPID = $_POST['sortby'];
    
// A switch case is used to display the topics avalible in which ever order selected.
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
        // This is a unique one as it also filters out non project topics
        $sql = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID WHERE topic.project_ID IS NOT NULL GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.project_ID";
        $Result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($Result) > 0){
            while($resultA = mysqli_fetch_array($Result)){
                TopicChecker($resultA, $conn);
            }
        } else {
            echo "Sorry no available results";
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
            $(".lightB").addClass("border-light");
        }
    })
</script>