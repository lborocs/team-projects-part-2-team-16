
<style>

body {
  display: flex;
  flex-direction: column;
  }
  .HeightShown {
  flex: 1;
  min-height: 80vh;
  }
</style>

<?php
//Connect to Database
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error.";
}
session_start();

// Obtain the topic ID that the posts are in to be able to display them
$Current_Topic = $_GET['Post_topic_ID'];
$INT_ID = (int)$Current_Topic;

function TopicChecker($resultA, $conn)
{
    if ($resultA['project_ID'] !== null) {
        $prjTEST = $resultA['project_ID'];
        $sqlProject = "SELECT user_ID FROM tasks WHERE project_id = $prjTEST AND user_ID = $_SESSION["user_ID"]";
        $resultProject = mysqli_query($conn, $sqlProject);
        $UserID_LIST = mysqli_fetch_assoc($resultProject)
        if ($_SESSION["role"] == "Employee") {
        if ($UserID_LIST['user_ID'] != $_SESSION["user_ID"]) {
            header("Location: view_topics.php");
            break;
                }
            }
        }
      }

$sqlTopics = "SELECT project_ID FROM topics WHERE topic_ID = $INT_ID";
$resultTopics = mysqli_query($conn, $sqlTopics);

while ($resultA = mysqli_fetch_array($resultTopics)) {
    TopicChecker($resultA, $conn);
}


// Obtain topic name to Display topic name at the top of the page
$sqlTopicName = "SELECT title FROM topics WHERE topic_ID = $INT_ID";
$resultTN = mysqli_query($conn, $sqlTopicName);
$TopicArray = mysqli_fetch_assoc($resultTN);
$TopicTitle = $TopicArray['title'];

?>

<?php
// Navbar displayed depending on whos logged in

if (!isset($_SESSION["role"])) {
  echo "<script>window.location.href='./login.php'</script>";
} else if ($_SESSION["role"] == "Manager") {
  $taskcreate = "link-dark";
  $topicview = "link-dark";
  $dashview = "link-dark";
  include "./navbar_m.php";
} else if ($_SESSION["role"] == "TL") {
  $topicview = "link-dark";
  $taskcreate = "link-dark";
  $taskview = "link-dark";
  $dashview = "link-dark";
  include "./navbar_tl.php";
} else if ($_SESSION["role"] == "Employee") {
  $topicview = "link-dark";
  $dashview = "link-dark";
  include "./navbar_e.php";
}

?>

<link rel="stylesheet" type="text/css" href="./posts.css">
<link rel="icon" type="image/x-icon" href="./logo.ico">
<style>
  .card_img {
    height: 175px;
    width: 198px;
    border-radius: 3px;
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
  </style>

<div class="container">
  <div class="title row py-4">
    <!-- Display topic title -->
    <h1><?php echo "Topic: " . $TopicTitle; ?></h1>
  </div>
  <div class="row mb-4">
    <div class="mb-3 col-10">
      <div class="input-group">
        <!-- Search for a post asynchronusly -->
        <input id="IDsearch" type="text" name="IDsearch" class="form-control" placeholder="Enter Post:" aria-label="Text input with dropdown button">
        <input type="hidden" name="Post_topic_ID" value="<?php echo $INT_ID; ?>">
        <!-- Dropdown to sort the posts asynchronusly -->
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Sort By</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><button name="ABC" class="dropdown-item" type="button" onclick="change('ABC')">Alphabetically</button></li>
          <li><button name="Date" class="dropdown-item" type="button" onclick="change('Date')">Date Posted</button></li>
          <li><button name="View" class="dropdown-item" type="button" onclick="change('View')">Views</button></li>
        </ul>
      </div>
    </div>

    <div class="col-2">
      <!-- If your a Manager you will have a dropdown displayed to either delete the current topic or create a post -->
      <?php
      if ($_SESSION["role"] == "Manager") {
      ?>

        <form>

          <button class="btn btn-primary input-group edit dropdown-toggle no-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">Edit</button>

          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <button class="dropdown-item" type="button" onclick="window.location.href='./create_post.php?topic_ID=<?php echo $Current_Topic ?>';">Create Post</button>
            </li>
            <li>
              <button class="dropdown-item" type="button" onclick="deletetopic()">Delete Topic</button>
            </li>
          </ul>
        </form>

        <!-- Passes the topic ID to a page to handle the deletion and asks for confimation of the topic delete -->
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
      } else {
      ?>
        <!-- If your not the manager you only have the optin to create a post on that topic -->
        <button type="button" class="btn btn-primary input-group no-dark" onclick="window.location.href='./create_post.php?topic_ID=<?php echo $Current_Topic ?>';">Create Post</button>

      <?php
      }
      ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- Passes search input along with needed varibles to search for a post asynchronusly -->
  <script>
    function AsyncSearch() {
      var searchInput = $("#IDsearch").val();

      if (searchInput.trim().length > -1) {
        $.ajax({
          type: "POST",
          url: "asyncposts.php",
          data: {
            search: searchInput,
            asyncINT_ID: "<?php echo $INT_ID; ?>",
            asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"
          },
          success: function(response) {
            $("#async").html(response);
          },
          error: function(e) {
            console.error('Error');
          }
        });
      }
    }
    $("#IDsearch").on('input', AsyncSearch);

    // Passes sort by button pressed along with needed varibles to sort posts asynchronusly
    function change(sortby) {
      $.ajax({
        type: "POST",
        url: "asyncposts.php",
        data: {
          sortby: sortby,
          asyncINT_ID: "<?php echo $INT_ID; ?>",
          asyncCOLOR: "<?php echo $_SESSION["lightmode"]; ?>"
        },

        success: function(response) {
          $("#async").html(response);

        },
        error: function(e) {
          console.error('Error');
        }

      });
    }
  </script>

  <div id="async" class="container con1 row mx-auto">

      <?php

      // Adds 1 to the number of views a topic has when a topic is selected
      if (isset($Current_Topic)) {
        $sql = "SELECT views FROM topics WHERE topic_ID = $INT_ID";
        $resultADD = mysqli_query($conn, $sql);
        $changingview = mysqli_fetch_assoc($resultADD);
        $currentViews = $changingview['views'];
        $newViews = $currentViews + 1;

        $updateSqlViews = "UPDATE topics SET views = $newViews WHERE topic_ID = $INT_ID";
        if (mysqli_query($conn, $updateSqlViews)) {
        } else {
          echo "Error";
        }
      }

      // Obtains all the posts and content to display for that topic
      $sql = "SELECT title, content, img_url, post_ID  FROM posts WHERE topic_ID = $INT_ID ORDER BY title ASC";
      $Result = mysqli_query($conn, $sql);

      
      if(mysqli_num_rows($Result) > 0){
        while($resultA = mysqli_fetch_array($Result)){
          // If no photo was selected then a neutral user icon is displayed
          if ($resultA["img_url"] == 'null') {
            $PIC = "grey.png";
          } else {
            $PIC = $resultA["img_url"];
          }
  
          // If there are posts in the topic they get displayed as cards contianing the photo and post title.
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
        } else {
    
          echo "<div style='text-align: center;'>";
          echo "Sorry no available results";
          echo "</div>";
    
        }

      ?>
    </div>
</div>

<!-- Display the footer at the bottom of the page -->
<?php include "./footer.php"; ?>

<!-- Applies dark mode classes to nessasery elements if dark mode is enabled -->
<script>
  <?php
  if ($_SESSION["lightmode"] == 1) {
    $colour = "text-light bg-dark";
  } else {
    $colour = "";
  }
  ?>

  $(document).ready(function() {
    <?php if ($colour == "text-light bg-dark") { ?>
      $("*").each(function() {
        if ($(this).hasClass("no-dark") == false && $(this).parents("header").length == 0) {
          $(this).addClass("text-light bg-dark");
        }
      });
    <?php } ?>
  })
</script>