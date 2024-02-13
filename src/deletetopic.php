 <?php
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $INT_ID = $_POST["deleteID"];

  $sqlTopic = "DELETE FROM topics WHERE topic_ID = $INT_ID";
  $ResultTopic = mysqli_query($conn, $sqlTopic);

  $sqlPost = "DELETE FROM posts WHERE topic_ID = $INT_ID";
  $ResultPost = mysqli_query($conn, $sqlPost);

  if ($ResultTopic && $ResultPost) {
    header("Location: view_topics.php");
    exit;
  } else {
    echo "Error";
  }
}
?>