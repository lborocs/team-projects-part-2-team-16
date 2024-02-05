<?php
                include "db_connection.php";
                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn) {
                    echo "Connection Error.";
                    exit;
                }
                
                $sql2 = "SELECT content, FROM  response, WHERE post_ID = 1, ORDER BY Date";
                

                $result = mysqli_query($conn, $sql);
                echo
                if (!$result) {
                    echo "Connection Error.";
                    exit;
                }
                $Replies = mysqli_fetch_all($result);

                while($reply = mysqli_fetch_array($Replies)){

                  echo '
        <div class="row row-third-height my-4 response-row">
        <div class="col">
          <p class="response-number"> ' . $reply["user_ID"] . ' </p>
          <p>' . $reply["content"] . '</p>
        </div>
      </div>'; 
    }
  ?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7Rxnatzjc6, DSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <style>
    .row-third-height {
      height: 20vh;
      background-color: white;
      border: 1px solid #000;
      border-radius: 15px;
      padding: 10px;
    }

    .row-tenth-height {
      height: 10vh;
    }

    .response-number {
      border-bottom: 1px solid #ccc;
    }

    .question {
      border-bottom: 1px solid #ccc;
      background-color: rgba(227, 207, 207, 0.303);
    }

    .response-row {
      margin-left: 20%;
    }

    .image-container {
      max-width: 100%;
      max-height: 100%;
      /* Set the container to be half the size */
    }

    .vertical-line {
      border-right: 1px solid #ccc;
      /* Add a vertical line on the right side */
    }

    .max-width-100 {
      max-width: 100%;
    }

    .max-height-100 {
      max-height: 18vh;
    }
  </style>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Managers Dash</title>

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

      .dropdown-item {
        cursor: pointer;
      }
    </style>

    <link href="./headers.css" rel="stylesheet">
  </head>

</head>

<body>
  <script>
    function settings() {
      window.location.href = "./settings_e.html";
    };
    function logout() {
      window.location.href = "./login.html";
    };
    function addReply() {
      window.location.href = "./login.html";
    };
  </script>
  <header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="./dashboard_e.html" class="nav-link px-2 link-dark">Dashboard</a>
          </li>
          <li><a href="./view_topics_e.html" class="nav-link px-2 link-dark">Topics</a></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" action="./view_topics_e.html">
          <input type="search" class="form-control" placeholder="Search Topics" aria-label="Search">
        </form>

        <div class="dropdown text-end">
          <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="./icon.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="./create_topic_e.html">Create New Topic...</a></li>
            <li><a class="dropdown-item" href="#" onclick="settings()">Settings</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#" onclick="logout()">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <div class="container" style="height: 10vh;">
    <button type="button" class="btn btn-dark" onclick="window.location.href='view_posts_m.php';">Back</button>
    <div class="container">
      <div class="row row-third-height my-4">
        <div class="col-2 vertical-line  d-flex justify-content-center align-items-center">
          <img src="empty-pfp.jpg" alt="Question Image" class=" max-width-100 max-height-100">
        </div>
        <div class="col">
          <p class="response-number">Post Title: Question or knowledge here</p>
          <p class="response-number">By Employee 1</p>
          <p>Question or knowledge is shared here... </p>
        </div>
      </div>


      <div class="input-group">
        <input type="Reply" class="form-control rounded" placeholder="Type reply here..." aria-label="Reply"
          aria-describedby="search-addon" />
        <button type="button" class="btn btn-outline-primary" onclick = "addReply()">Reply</button>
      </div>


    </div>
  </div>
</body>

</html>
