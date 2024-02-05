<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['reply'];
    $user_ID = $_SESSION['user_ID'];
    $post_ID = $user_ID = $_SESSION['post_ID'];
    $insertQuery = "insert into response (response_ID , user_ID, post_ID, content, Date)
                                 values ($response_ID, $user_ID, '$post_ID', '$content', CURRENT_DATE);";
     

  
    echo 'Reply added successfully!';
} else {
    // Handle other request methods or invalid requests
    http_response_code(405); // Method Not Allowed
    echo 'Invalid request method';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $insertQuery = "DELETE FROM responses WHERE response_ID = $response_ID;";
     

    
    echo 'Reply added successfully!';
} else {
    // Handle other request methods or invalid requests
    http_response_code(405); // Method Not Allowed
    echo 'Invalid request method';
}
?>