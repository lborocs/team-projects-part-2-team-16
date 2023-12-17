<html>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<head>
    <link rel="icon" type="image/x-icon" href="./logo.ico">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Create Account</title>
	</head>
	<script>
	  function cancel(){
		  window.location.href = './login.php';
	  };
	</script>
	<body>
  <?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  set_error_handler("handleErrors");
  function handleErrors($errno, $errstr, $errfl, $errln){
          $errstr = addslashes($errstr);
          echo $errstr;
          die();
  }
  //end of error handling
    function structure_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
    $firstname = $surname = $email = $pass1 = $pass2 = $encryptedPassword = $code = $ErrorMessage = "";
    $accountCreated = "none";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $valid = true;
      $firstname = structure_input($_POST["firstnameField"]);
      $surname = structure_input($_POST["surnameField"]);
      $email = structure_input($_POST["emailField"]);
      $pass1 = structure_input($_POST["password1field"]);
      $pass2 = structure_input($_POST["password2field"]);
      $code = structure_input($_POST["codeField"]);
      //email check
      if(preg_match('/^(([A-Za-z0-9])|([A-Za-z0-9.-_]+[A-Za-z0-9])){1,20}$/',$email)){
        $email = $email."@make-it-all.co.uk";
      }else{
        $valid = false;
        $ErrorMessage = "Error: Email format incorrect.";
      }
      //password checks
      if($pass1 == $pass2){
        if(str_contains(strtolower($pass1),strtolower($firstname))){
          $valid = false;
          $ErrorMessage = "Password must not contain firstname or secondname.";
        }else if(str_contains(strtolower($pass1),strtolower($surname))){
          $valid = false;
          $ErrorMessage = "Password must not contain firstname or secondname.";
        }
        //regex for password
        $uppercaseCheck = '/[A-Z]/';
        $lowercaseCheck = '/[a-z]/';
        $digitCheck = '/\d/';
        $specialCharCheck = '/[!@#$%^&*(),.?":{}|<>]/';
        $isUppercase = preg_match($uppercaseCheck,$pass1);
        $isLowercase = preg_match($lowercaseCheck,$pass1);
        $isDigit = preg_match($digitCheck,$pass1);
        $isSpecialChar = preg_match($specialCharCheck,$pass1);
        $isLengthValid = strlen($pass1) >= 8;
        if($isUppercase && $isLowercase && $isDigit && $isSpecialChar && $isLengthValid){
          $encryptedPassword = hash('sha256', $pass1);
        }else{
          $valid = false;
          $ErrorMessage = "Passwords format incorrect.";
        }
        //
      }else{
        $valid = false;
        $ErrorMessage = "Passwords must match.";
      }
      if(!preg_match("/^(([A-Za-z])|([A-Za-z])+([A-Za-z ])+([A-Za-z])){1,20}$/",$firstname)){
        $valid = false;
        $ErrorMessage = "Error: Firstname Format Invalid";
      }else if(!preg_match("/^(([A-Za-z])|([A-Za-z-])+([A-Za-z])){1,20}$/",$surname)){
        $valid = false;
        $ErrorMessage = "Error: Surname Format Invalid";
      }else if(!preg_match("/^[A-Z]{4}-[A-Z]{4}-[A-Z]{4}-[A-Z]{4}$/",$code)){
        $valid = false;
        $ErrorMessage = "Error: Invite Code Format Invalid";
      }
      //Decide what action to take
      if($valid){
        include "db_connection.php";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
          echo "Connection Error." ;
          exit;
        }
        $sql = "SELECT expires
                FROM   activeInviteCodes 
                WHERE  code ='".$code."'";

        $result = mysqli_query($conn,$sql);
        if (!$result) {
            echo "Connection Error.";
            exit;
        }
      
        if (mysqli_num_rows($result) == 0) {
          $ErrorMessage = 'Code Invalid, please try again.';
        }else{
          mysqli_query($conn,"DELETE FROM activeInviteCodes WHERE code ='".$code."'");
          $currentDate = date("Y-m-d");
          $expiryDate = mysqli_fetch_assoc($result)["expires"];
          if($currentDate <= $expiryDate){
            $sql = "INSERT INTO users
                VALUES (NULL,'".$email."','".$encryptedPassword."','grey','".$firstname."','".$surname."','Employee',0)";
            $result = mysqli_query($conn,$sql);
            if (!$result) {
              echo "Connection Error.";
              exit;
            }
            $accountCreated = "block";
            $ErrorMessage = '';
          }else{
            $ErrorMessage = "Code used is outdated, please request a new one from a member of staff.";
          }
        }
      }
    }
  ?>
	  <div class="bg-dark text-secondary px-4 py-5 text-center" style="margin:0px; padding:0px;">
			<div class="py-5">
			  <h1 class="display-5 fw-bold text-white">Welcome to Make-It-All.</h1>
			</div>
		</div>
	  <div style = "padding:2%;">
      <div class="alert alert-success alert-dismissible fade show" role="alert" style = "display:<?php echo $accountCreated; ?>;">
        Success! Account has been created. Welcome to make-it-all! <a href="./login.php" class="alert-link">Log in now.</a>. 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <div class="m-0 border-0">
        <form class="row g-3" method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
            <input id = "codeField" name="codeField" oninput = "checkCode(this.value)" type="text" class="form-control" 
            required="" <?php  echo 'value="'; if (isset($_GET['code'])){echo $_GET['code'];};if (isset($_POST['codeField'])){echo $_POST['codeField'];}; echo '"'; if (isset($_GET['code'])|isset($_POST['codeField'])){echo' readonly';} ?>>
            <div id = "codeStatus" class=""></div>
          </div>
          <div class="col-md-12 align-content-center" style="width:100%;">
            <p style = "color:red;"><?php echo $ErrorMessage; ?></p>
          </div>
          <div class="col-md-12 align-content-center" style="width:100%;">
            <button class="btn btn-primary" style="width:47%; margin:0% 1%" type="submit">Create Account</button>
            <button class="btn btn-secondary" style="width:47%; margin:0% 1%" type="button" onclick = "cancel()">Cancel</button>
          </div>
          <script>
            function checkName(name,n) {
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
              if(nameField.value == ""){
                nameStatus.textContent = '';
                nameStatus.className = '';
                nameField.className = 'form-control';
              }else if (isValid) {
                nameStatus.textContent = 'Looks Good!';
                nameStatus.className = 'valid-feedback';
                nameField.className = 'form-control is-valid';
              } else {
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
              var validCharsCheck = /^(([A-Za-z0-9])||([A-Za-z0-9.-_]+[A-Za-z0-9])){1,20}$/;
              var isValid = validCharsCheck.test(email);
              var emailStatus = document.getElementById('emailStatus');
              var emailField = document.getElementById('emailField');
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
              var uppercaseCheck = /[A-Z]/;
              var lowercaseCheck = /[a-z]/;
              var digitCheck = /\d/;
              var specialCharCheck = /[!@#$%^&*(),.?":{}|<>]/;
              var isUppercase = uppercaseCheck.test(password);
              var isLowercase = lowercaseCheck.test(password);
              var isDigit = digitCheck.test(password);
              var isSpecialChar = specialCharCheck.test(password);
              var isLengthValid = password.length >= 8;
              var passwordStatus = document.getElementById('password1status');
              var passwordField = document.getElementById('password1field');

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
              var password1 = document.getElementById('password1field');
              var password2 = document.getElementById('password2field');
              var passwordStatus = document.getElementById('password2status');

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
              var codeFormat = /^[A-Z]{4}-[A-Z]{4}-[A-Z]{4}-[A-Z]{4}$/;
              var isValid = codeFormat.test(inviteCode);
              var codeStatus = document.getElementById('codeStatus');
              var codeField = document.getElementById('codeField');
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
            <?php
              if (isset($_GET['code'])|isset($_POST['codeField'])){
                echo 'checkCode(document.getElementById("codeField").value);';
              }
            ?>
          </script>
        </form>
      </div>
    </div>
  </body>
</html>