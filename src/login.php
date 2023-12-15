<!doctype html>
<html lang="en">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Hugo 0.84.0">
  <title>Information Management System · Login</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">



  <!-- Bootstrap core CSS -->
  <link href="/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <!-- Favicons -->
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
  </style>


  <!-- Custom styles for this template -->
  <link href="./signin.css" rel="stylesheet">
</head>
<script>
  <?php 
  function createAcc() {
    window.location.href = "./create.php";
    return false;
  }
  ?>
  </script>
</script>

<body class="text-center">
<?php
  function structure_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  $EnteredPassword = $EnteredUsername = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $HashedPassword = hash("sha-256",structure_input($_POST["password"]));
    $EnteredUsername = structure_input($_POST["username"]);

    if (!$conn) {
      echo "Unable to connect to DB: " . mysqli_error();
      exit;
    }
    
    if (!mysqli_select_db("mydbname")) {
      echo "Unable to select mydbname: " . mysqli_error();
      exit;
    }
    
    $sql = "SELECT encrypted_password
            FROM   users
            WHERE  username =". $EnteredUsername .";"

    $result = mysqli_query($sql);

    if (!$result) {
        echo "<script><alert>Error, Please Try Again.</alert></script>";
        exit;
    }
    
    if (mysqli_num_rows($result) == 0) {
      echo "<script><alert>Details Incorrect, Please Try Again.</alert></script>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
      if ($row["encrypted_password"] == $HashedPassword){
        echo "<script><alert>Logged In.</alert></script>";
      }else{
        echo "<script><alert>Details Incorrect, Please Try Again.</alert></script>";
        exit;
      }
    }
  }
?>
  <main class="form-signin">
    <form method = "POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <img class="mb-4" src="./logo.png" alt="" width="240" height="60">

      <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

      <div class="form-floating">
        <input type="text" name="username" class="form-control" id="username" placeholder="Username">
        <label for="username">Username</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="password" name = "password" placeholder="Password">
        <label for="floatingPassword">Password</label>
      </div>
      <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in </button>
      <button class="w-100 btn btn-lg btn-secondary" type="button" onclick="createAcc()" style="margin:1% 0%">Create
        Account</button>
    </form>
  </main>



</body>

</html>