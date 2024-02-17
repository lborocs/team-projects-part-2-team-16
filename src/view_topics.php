<!DOCTYPE html>
<html lang="en">

<html class = "<?php echo $colour;?>">

<head>
    <title>Topics</title>

    <link rel="stylesheet" type="text/css" href="topics.css">
    <link rel="icon" type="image/x-icon" href="./logo.ico">


    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Dash</title>

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



<body">
<div class="HeightShown">



<?php
include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
      echo "Connection Error." ;
    }
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
				$topicview = "border-bottom border-primary link-primary";
				$dashview = "link-dark";
				include "./navbar_m.php";
			}else if($_SESSION["role"] == "TL"){
				$topicview = "border-bottom border-primary link-primary";
				$taskcreate = "link-dark";
				$taskview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_tl.php";
			}else if($_SESSION["role"] == "Employee"){
				$topicview = "border-bottom border-primary link-primary";
				$dashview = "link-dark";
				include "./navbar_e.php";
			}
		?>
<div> 
<div class="input-group mb-3 Search con2">

<input id="IDsearch" type="text" name="IDsearch" class="form-control" placeholder="Enter Topic:" aria-label="Text input with dropdown button">

<button class="btn btn-outline-secondary dropdown-toggle lightB" type="button" data-bs-toggle="dropdown" aria-expanded="false">Sort By</button>

<ul class="dropdown-menu dropdown-menu-end">
<li>
    <button name="ABC" class="dropdown-item" type="button" onclick="change('ABC')">Alphabetically</button>
</li>
<li>
    <button name="Date" class="dropdown-item" type="button" onclick="change('Date')">Date Posted</button>
</li>
<li>
    <button name="Post" class="dropdown-item" type="button" onclick="change('Post')">Posts</button>
</li>
<li>
    <button name="View" class="dropdown-item" type="button" onclick="change('View')">Views</button>
</li>
<li>
    <button name="Project" class="dropdown-item" type="button" onclick="change('Project')">Projects Only</button>
</li>
</ul>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <button type="button" class="btn btn-primary input-group mb-3 createPost conButton"
        onclick="window.location.href='./create_post.php';">Create Topic</button>






<script>

function AsyncSearch() {
        var searchInput = $("#IDsearch").val();

        if (searchInput.trim().length > -1) {
            $.ajax({
                type: "POST",
                url: "asynctopics.php",
                data: {
                    search: searchInput,
                    asyncUSER_ID: "<?php echo $_SESSION["user_ID"]; ?>",
                    asyncROLE_ID: "<?php echo $_SESSION["role"]; ?>" ,
                    asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"},
                success: function (response) {
                    $("#async").html(response);
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
               url: "asynctopics.php",
               data:
                {   sortby: sortby, 
                    asyncUSER_ID: "<?php echo $_SESSION["user_ID"]; ?>",
                    asyncROLE_ID: "<?php echo $_SESSION["role"]; ?>",
                    asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"},

               success:
                function (response){

                   $("#async").html(response); 
                },
               error:
                function (e){                  
                   console.error('Error');
               }

           });
       }

</script>

<div  id="async" class="con1">
<?php

function TopicList($resultA) {
    return '<div type="button" style="top: 495px; overflow: hidden;" class="topic1 col-xl lightB"
        onclick="window.location.href=\'./view_posts.php?Post_topic_ID=' . $resultA["topic_ID"] . '\';">            
        <div style="display: inline-block; width: 100%">
            <p>' . $resultA["title"] . '</p>
            <div style="height: 20px;position: relative;">
                <span style="display: inline-block; font-size: 17px;position: absolute; left: -70px;width: 220px;bottom: 15px; ">
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
    if (($resultA['project_ID'] !== null)){
        $prjTEST = $resultA['project_ID'];
        $sqlProjectCheck = "SELECT project_ID FROM project";
        $resultProjectCheck = mysqli_query($conn, $sqlProjectCheck);
        while($ProjectID_LIST = mysqli_fetch_assoc($resultProjectCheck)){
            if (($ProjectID_LIST['project_ID'] == $prjTEST)) {
                if(($_SESSION["role"] == "Manager")){
                    echo TopicList($resultA);
                }
                else {
                $sqlProject = "SELECT user_ID FROM tasks WHERE project_id = $prjTEST";
                $resultProject = mysqli_query($conn, $sqlProject);
                    while($UserID_LIST = mysqli_fetch_assoc($resultProject)){
                        if($_SESSION["role"] == "TL"){
                            $sqlTL = "SELECT team_leader FROM project WHERE project_ID = $prjTEST";
                            $resultTL = mysqli_query($conn, $sqlTL);
                            while($TLID_LIST = mysqli_fetch_assoc($resultTL)){
                                if($_SESSION["user_ID"] == $TLID_LIST["team_leader"]){
                                    echo TopicList($resultA);
                                    break 2;
                                } 
                            }
                        }
                        else  {
                            if (($UserID_LIST['user_ID'] == $_SESSION["user_ID"])){
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

if(isset($_GET['NavbarTopic'])){
    $searchInput = $_GET['NavbarTopic'];
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


else{
$sqlTopics = "SELECT topic.topic_ID, topic.title, topic.views, topic.project_ID, COUNT(post.topic_ID) FROM topics topic LEFT JOIN posts post ON topic.topic_ID = post.topic_ID GROUP BY topic.topic_ID, topic.title, topic.views ORDER BY topic.title";
$resultTopics = mysqli_query($conn, $sqlTopics);

while ($resultA = mysqli_fetch_array($resultTopics)) {
    
    TopicChecker($resultA, $conn);

}
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
                if ($(this).hasClass("no-dark") == false && $(this).parents("header").length == 0) {
                    $(this).addClass("text-light bg-dark");
                }
            });
            $(".lightB").addClass("border-light");
        }
    })
</script>
</body>