<!--
This file, in conjunction with createAsync.php is responsible for the creation, verification and  storage of new accounts.
A user will enter required details to create an account and either press create or cancel.
A user must have a password which meets requiremetns, as well as an invite code - given to them by another user of the system.
-->
<html>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<head>
    <link rel="icon" type="image/x-icon" href="./imgs/logo.ico">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Create Account</title>
	</head>
	<script>
	  function cancel(){
      //redirects page to login.php
		  window.location.href = './login.php';
	  };
	</script>
	<body>
  <?php
    $firstname = $surname = $email = $pass1 = $pass2 = $encryptedPassword = $code = $ErrorMessage = "";
    $accountCreated = "none";
  ?>
	  <div class="bg-dark text-secondary px-4 py-5 text-center" style="margin:0px; padding:0px;">
			<div class="py-5">
			  <h1 class="display-5 fw-bold text-white">Welcome to Make-It-All.</h1>
			</div>
		</div>
    <!-- everything id used with createAsync page -->
    <div id="everything">
      <div style = "padding:2%;">
        <!-- div below is used to confirm to a user when an account has been successfuly created. toogled to display via $accountCreated -->
        <div class="alert alert-success alert-dismissible fade show" role="alert" style = "display:<?php echo $accountCreated; ?>;">
          Success! Account has been created. Welcome to make-it-all! <a href="./login.php" class="alert-link">Log in now.</a>. 
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="m-0 border-0">
          <!-- div below the form allowing user to enter each value, the form doesnt have a submit type or a submit button, to be
          explained later -->
          <form id="createForm" class="row g-3" >
            <!-- each field activates its own function, when user types, to check the entered value is valid. You will see some inputs
          also pass in their own current value into the function -->
            <div class="col-md-4">
              <label for="firstnameField" class="form-label">Firstname(s)</label>
              <input id="firstnameField" name="firstnameField" oninput=(checkName(this.value,true)) type="text" class="form-control"  required="">
              <div id="firstnameStatus"class=""></div>
            </div>
            <div class="col-md-4">
              <label for="surnameField" class="form-label">Surname</label>
              <input id="surnameField" name="surnameField" oninput=(checkName(this.value,false)) type="text" class="form-control" value="" required="">
              <div id="surnameStatus"class=""></div>
            </div>
            <div class="col-md-4">
              <label for="emailField" class="form-label">Username</label>
              <div class="input-group has-validation">
                <input id = "emailField" name="emailField" type="text" class="form-control" 
                aria-describedby="inputGroupPrepend3" required="" oninput="checkEmailValid(this.value)">
                <span class="input-group-text" id="basic-addon2">@make-it-all.co.uk</span>
                <div id = "emailStatus" class=""></div>
              </div>
            </div>
            <div class="col-md-3">
              <label for="password1field" class="form-label">Password</label>
              <input id = "password1field" name="password1field" type="password" class="form-control"
                required="" oninput="checkPasswordStrength(this.value)">
              <div class="" id = "password1status"></div>
            </div>
            <div class="col-md-3">
              <label for="password2field" class="form-label">Verify Password</label>
              <input id = "password2field" name="password2field" type="password" class="form-control" required=""
              oninput="checkPasswordMatch()">
              <div  id = "password2status" class=""></div>
            </div>
            <div class="col-md-6">
              <label for="codeField" class="form-label">Invite Code</label>
              <!-- inputs code from link, locks input field to avoid mistyping over code -->
              <input id = "codeField" name="codeField" oninput = "checkCode(this.value)" type="text" class="form-control" 
              required="" <?php  echo 'value="'; if (isset($_GET['code'])){echo $_GET['code'];};if (isset($_POST['codeField'])){echo $_POST['codeField'];}; echo '"'; if (isset($_GET['code'])|isset($_POST['codeField'])){echo' readonly';} ?>>
              <div id = "codeStatus" class=""></div>
            </div>
            <!-- used to dislay any errors which the user may need to know -->
            <div class="col-md-12 align-content-center"  style="width:100%;">
              <p style = "color:red;" id = "eMessage"><?php echo $ErrorMessage; ?></p>
            </div>
            <div class="col-md-12 align-content-center" style="width:100%;">
            <!-- buttons bellow used to return to login(via cancel()) or to trigure the create function -->
              <button class="btn btn-primary" style="width:47%; margin:0% 1%" type="button" onclick="create()">Create Account</button>
              <button class="btn btn-secondary" style="width:47%; margin:0% 1%" type="button" onclick = "cancel()">Cancel</button>
            </div>
            <script>
              function checkName(name,n) {
                //checks format of names entered locally (whilst typing)
                //n value used to switch between checking password # 1 or 2
                var isValid;
                if (n){
                  var validCharsCheck = /^(([A-Za-z])||([A-Za-z])+([A-Za-z ])+([A-Za-z])){1,20}$/;
                  var isValid = validCharsCheck.test(name);
                  var nameStatus = document.getElementById('firstnameStatus');
                  var nameField = document.getElementById('firstnameField');
                }else{
                  var validCharsCheck = /^(([A-Za-z])||([A-Za-z-])+([A-Za-z])){1,20}$/;
                  var isValid = validCharsCheck.test(name);
                  var nameStatus = document.getElementById('surnameStatus');
                  var nameField = document.getElementById('surnameField');
                }
                //responds to user via confirmation/error messages
                if(nameField.value == ""){
                  nameStatus.textContent = '';
                  nameStatus.className = '';
                  nameField.className = 'form-control';
                }else if (isValid) {
                  nameStatus.textContent = 'Looks Good!';
                  nameStatus.className = 'valid-feedback';
                  nameField.className = 'form-control is-valid';
                } else {
                  //n is used to determine whether we are accessing a first or lastname
                  if (n){
                    nameStatus.textContent = 'Please provide a valid name. (no non-alphabet characters)';
                  }else{
                    nameStatus.textContent = 'Please provide a valid name. (no non-alphabet characters, excluding "-")';
                  }
                  nameStatus.className = 'invalid-feedback';
                  nameField.className = 'form-control is-invalid';
                }
              }
              function checkEmailValid(email) {
                //checks format of email/username entered locally (whilst typing)
                var validCharsCheck = /^(([A-Za-z0-9])||([A-Za-z0-9.-_]+[A-Za-z0-9])){1,20}$/;
                var isValid = validCharsCheck.test(email);
                var emailStatus = document.getElementById('emailStatus');
                var emailField = document.getElementById('emailField');
                //responds to user via confirmation/error messages
                if(emailField.value == ""){
                  emailStatus.textContent = '';
                  emailStatus.className = '';
                  emailField.className = 'form-control';
                }else if (isValid) {
                  emailStatus.textContent = 'Format Appears Valid.';
                  emailStatus.className = 'valid-feedback';
                  emailField.className = 'form-control is-valid';
                } else {
                  emailStatus.textContent = 'Please provide a valid Email prefix. (1 uppercase, 1 lowercase, 1 number, up to 20 characters)';
                  emailStatus.className = 'invalid-feedback';
                  emailField.className = 'form-control is-invalid';
                }
              }
              function checkPasswordStrength(password) {
                //checks strength of password entered locally (whilst typing)
                checkPasswordMatch();
                //creates regex for each requirement
                var uppercaseCheck = /[A-Z]/;
                var lowercaseCheck = /[a-z]/;
                var digitCheck = /\d/;
                var specialCharCheck = /[!@#$%^&*(),.?":{}|<>]/;
                //checks against regex, shown above
                var isUppercase = uppercaseCheck.test(password);
                var isLowercase = lowercaseCheck.test(password);
                var isDigit = digitCheck.test(password);
                var isSpecialChar = specialCharCheck.test(password);
                var isLengthValid = password.length >= 8;
                var passwordStatus = document.getElementById('password1status');
                var passwordField = document.getElementById('password1field');

                //changes the status of elements to respond to user.
                if(passwordField.value == ""){
                  passwordStatus.textContent = '';
                  passwordStatus.className = '';
                  passwordField.className = 'form-control';
                }else if (isUppercase && isLowercase && isDigit && isSpecialChar && isLengthValid) {
                  passwordStatus.textContent = 'Looks good!';
                  passwordStatus.className = 'valid-feedback';
                  passwordField.className = 'form-control is-valid';
                } else {
                  passwordStatus.textContent = 'Please provide a valid Password. (1 uppercase, 1 lowercase, 1 special, 1 number, min length 8)';
                  passwordStatus.className = 'invalid-feedback';
                  passwordField.className = 'form-control is-invalid';
                }
              }
              function checkPasswordMatch() {
                //checks passwords match entered locally (whilst typing)
                var password1 = document.getElementById('password1field');
                var password2 = document.getElementById('password2field');
                var passwordStatus = document.getElementById('password2status');

                //checks format of passwords entered locally (whilst typing)
                if(password2.value == ""){
                  passwordStatus.textContent = '';
                  passwordStatus.className = '';
                  password2.className = 'form-control';
                }else if (password1field.value == password2field.value) {
                  passwordStatus.textContent = 'Looks good!';
                  passwordStatus.className = 'valid-feedback';
                  password2.className = 'form-control is-valid';
                } else {
                  passwordStatus.textContent = "Passwords don't match.";
                  passwordStatus.className = 'invalid-feedback';
                  password2.className = 'form-control is-invalid';
                }
              }
              function checkCode(inviteCode) {
                //checks format of invite code when entered.
                var codeFormat = /^[A-Z]{4}-[A-Z]{4}-[A-Z]{4}-[A-Z]{4}$/;
                var isValid = codeFormat.test(inviteCode);
                var codeStatus = document.getElementById('codeStatus');
                var codeField = document.getElementById('codeField');
                //responds to user via error/confirmation messages
                if(codeField.value == ""){
                  codeStatus.textContent = '';
                  codeStatus.className = '';
                  codeField.className = 'form-control';
                }else if (isValid) {
                  codeStatus.textContent = 'Invite code format valid!';
                  codeStatus.className = 'valid-feedback';
                  codeField.className = 'form-control is-valid';
                } else {
                  codeStatus.textContent = 'Please provide a valid Invite Code, provided by existing employee.';
                  codeStatus.className = 'invalid-feedback';
                  codeField.className = 'form-control is-invalid';
                }
              }
              <?php // used to check code that is entered via get, to show if code format is wrong (link therefore wrong)
                if (isset($_GET['code'])|isset($_POST['codeField'])){
                  echo 'checkCode(document.getElementById("codeField").value);';
                }
              ?>
            </script>
          </form>
        </div>
      </div>
    </div>
  </body>
  <script>
    function create() {
      var form = document.getElementById('createForm');

      // Check if the form is valid
      if (form.checkValidity()) {
          // Form is valid, proceed with creating account
          var data = {};
          //set data for POST via ajax
          data.firstnameField = document.getElementById('firstnameField').value;
          data.surnameField = document.getElementById('surnameField').value;
          data.emailField = document.getElementById('emailField').value;
          data.password1field = document.getElementById('password1field').value;
          data.password2field = document.getElementById('password2field').value;
          data.codeField = document.getElementById('codeField').value;

          // AJAX request
          $.ajax({
              url: 'createAsync.php',
              method: 'POST',
              data: data,
              success: function(response) {
                  // if ajax succeeds then replace evrything in html via post
                  document.getElementById('eMessage').innerText = ''; //clear errorMessage
                  $('#everything').html(response); // update displayed content
              },
              error: function(error) {
                  console.error(error); // log any errors
              }
          });
      } else {
          // if the is invalid, do nothing
          document.getElementById('eMessage').innerText = 'Pleaes fill in all fields.';
          return false;
      }
    }
  </script>
</html>