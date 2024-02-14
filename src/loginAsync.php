<!--This page works for the login.php page to either log the user in, or keep the enetred values appearing on screen whilst displaying an error.
This prevents refreshing of page and allows user to not have to enter both values entirely again. The html returned by this page is displayed in the login.php
page's "everything" div.-->
<script>
  function createAcc() {
    //redirects user to create account page
    window.location.href = "./create.php";
    return false;
  }

  </script>
</script>

<body class="text-center">
<?php
  //resets the session when user has logged out
  session_start();
  session_unset();
  session_destroy();
  session_write_close();
  setcookie(session_name(),'',0,'/');
  session_regenerate_id(true);
  function structure_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  $errorMessage = '';
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //we can be confident in this data as its already been processed to be in correct format by login.php page
    //performs hash on password entered by user
    $HashedPassword = hash("sha256",structure_input($_POST["password"]));
    //selects username entered by user
    $EnteredUsername = structure_input($_POST["username"]);

    include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
      echo "Connection Error." ;
      exit;
    }
    //selects user from database server-side
    $sql = "SELECT user_ID,role,icon,encrypted_pass,lightmode
            FROM   users
            WHERE  email ='".$EnteredUsername."'";

    $result = mysqli_query($conn,$sql);

    if (!$result) {
        echo "Connection Error.";
        exit;
    }
    $data = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) == 0) {
      $errorMessage = 'Details Incorrect, please try again.';
      //if no user with that username exists
    }else{
      if ($data["encrypted_pass"] == $HashedPassword){
        //if our passwords match with username then log user in
        session_start();
        //create session so used stays logged in between pages
        $_SESSION["user_ID"] = $data["user_ID"];
        $_SESSION["role"] = $data["role"];
        $_SESSION["icon"] = $data["icon"];
        $_SESSION["lightmode"] = $data["lightmode"];
        $_SESSION["expiry"]  = date("'m-d-Y')",mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));

        if(isset($_SESSION["role"])){
          //if values are set, redirect user to dashboard page
          echo '<script>window.location.href = "./dashboard.php";</script>';
        }else{
          $errorMessage = 'Details Incorrect, please try again.';
          //display error if not logged in
        }
      }else{
        //display error if password incorect
        $errorMessage = 'Details Incorrect, please try again.';
      }
    }
  }
?>
<!--Below is identical to the login page, bar one which is shown via comment.-->
<main class="form-signin">
  <form id = "loginForm">
    <img class="mb-4" src="./logo.png" alt="" width="300" height="70" style="display: block;margin-left: auto;margin-right: auto;border-radius:4px;border:solid black 1px;">

    <h1 class="h3 mb-3 fw-normal">Please Sign-In</h1>

    <div class="form-floating">
      <input type="text" name="username" class="form-control" id="username" placeholder="Username">
      <label for="username">Email</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="password" name = "password" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>
    <div><p style = "color:red;"><?php echo  $errorMessage; ?></p></div>
    <button class="w-100 btn btn-lg btn-primary" type="button" onclick="login()">Sign In </button>
    <button class="w-100 btn btn-lg btn-secondary" type="button" onclick="createAcc()" style="margin:1% 0%">Create
      Account</button>
  </form>
</main>
<?php
//if login fails, keep the entered values on screen to user doesnt have to re-enter
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  echo '<script>document.getElementById("username").value = "'.$_POST["username"].'";</script>';
  echo '<script>document.getElementById("password").value = "'.$_POST["password"].'";</script>';
}
?>
<script>
  username.addEventListener('keyup', function(event) {
        if (event.keyCode === 13) {
            login();
        }
    });
    password.addEventListener('keyup', function(event) {
        if (event.keyCode === 13) {
            login();
        }
    });
</script>