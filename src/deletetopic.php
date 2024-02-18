<?php
//Connect to Database
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  echo "Connection Error." ;
}

// Obtains the topic ID of the topic the manager chose to delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $INT_ID = $_POST["deleteID"];

  // Deletes the topic from the database
  $sqlTopic = "DELETE FROM topics WHERE topic_ID = $INT_ID";
  $ResultTopic = mysqli_query($conn, $sqlTopic);

  // Deletes the posts in selected topic from the database
  $sqlPost = "DELETE FROM posts WHERE topic_ID = $INT_ID";
  $ResultPost = mysqli_query($conn, $sqlPost);

  // Returns to viewtopic page once done
  if ($ResultTopic && $ResultPost) {
    header("Location: view_topics.php");
    exit;
  } else {
    echo "Error";
  }
}
?>