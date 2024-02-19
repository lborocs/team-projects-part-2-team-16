<!-- This page allows the user to type in their email and password to login. they can also access the create account page from
here. However they need to enter an invite code manually to create an account this way. This page works with loginAsync.php to
asynchronously return errors.-->
<html>
<html lang="en">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<head>
  <link rel="icon" type="image/x-icon" href="./imgs/logo.ico">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Hugo 0.84.0">
  <title>Make-It-All · Login</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>



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
  //sends user to create account page
  function createAcc() {
    window.location.href = "./create.php";
    return false;
  }

</script>

<body class="text-center">
<?php
//the following will delete all data stored relating to a previously logged in user.
  session_start();
  session_unset();
  session_destroy();
  session_write_close();
  setcookie(session_name(),'',0,'/');
  session_regenerate_id(true);
  $errorMessage = '';

?>
<!-- The following everything div is used to display errors with ajax without refreshing the page.-->
  <div id = "everything" class="container">
    <main class="form-signin">
      <!-- Login form is used to check both fields have been entered. There is no submit action dedicated here-->
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
        <!-- emessage is used to display errors in red to user -->
        <div><p id = "eMessage" style = "color:red;"><?php echo  $errorMessage; ?></p></div>
        <!--These two buttons are used to switch to create page or to submit the values inside the form (without submitting acutal form)-->
        <button class="w-100 btn btn-lg btn-primary" type="button" onclick="login()">Sign In </button>
        <button class="w-100 btn btn-lg btn-secondary" type="button" onclick="createAcc()" style="margin:1% 0%">Create
          Account</button>
      </form>
    </main>
</div>

</body>
<script>
  function login() {
    //used to take entered values from user and create a post request
      var form = document.getElementById('loginForm');

      // Check if the form is valid
      if (form.checkValidity()) {
          // Form is valid, proceed with creating account
          var data = {};
          data.username = document.getElementById('username').value;
          data.password = document.getElementById('password').value;

          // AJAX request
          $.ajax({
              url: 'loginAsync.php',
              method: 'POST',
              data: data,
              success: function(response) {
                  // Handle success
                  $('#everything').html(response); // Update displayed content
              },
              error: function(error) {
                  console.error(error); // Log error to the console
                  $('#eMessage').text('Details Incorrect, please try again.'); // display details are incorrect if ajax fails
              }
          });
      } else {
          // if the isn't valid, do nothing
          return false;
      }
    }
    //the following is so that users can also attempt to login via the enter key
    username.addEventListener('keyup', function(event) {
        // Check if the key pressed is 'Enter'
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

</html>