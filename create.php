<html>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Create Account</title>
	  </head>
	  <script>
	  function cancel(){
		window.location.href = './login.html';
	  };
	  </script>
	  <body>
	  <div class="bg-dark text-secondary px-4 py-5 text-center" style="margin:0px; padding:0px;">
			<div class="py-5">
			  <h1 class="display-5 fw-bold text-white">Welcome to Make-It-All.</h1>
			</div>
		</div>
	  <div style = "padding:2%;">
        <div class="bd-example-snippet bd-code-snippet"><div class="bd-example m-0 border-0">
        <form class="row g-3">
          <div class="col-md-4">
            <label for="validationServer01" class="form-label">First name</label>
            <input type="text" class="form-control is-valid" id="validationServer01"  required="">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="col-md-4">
            <label for="validationServer02" class="form-label">Last name</label>
            <input type="text" class="form-control is-valid" id="validationServer02" value="" required="">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="col-md-4">
            <label for="validationServerUsername" class="form-label">Username</label>
            <div class="input-group has-validation">
			<input type="text" class="form-control is-invalid" id="validationServerUsername" aria-describedby="inputGroupPrepend3" required="">
			<span class="input-group-text" id="basic-addon2">@make-it-all.co.uk</span>
              <div class="invalid-feedback">
                Please choose a username.
              </div>
            </div>
          </div>
		  <div class="col-md-3">
            <label for="validationServer05" class="form-label">Password</label>
            <input type="password" class="form-control is-invalid" id="validationServer05" required="">
            <div class="invalid-feedback">
              Please provide a valid Password.
            </div>
          </div>
		  <div class="col-md-3">
            <label for="validationServer06" class="form-label">Verify Password</label>
            <input type="password" class="form-control is-invalid" id="validationServer06" required="">
            <div class="invalid-feedback">
              Passwords don't match.
            </div>
          </div>
          <div class="col-md-6">
            <label for="validationServer03" class="form-label">Invite Code</label>
            <input type="text" class="form-control is-invalid" id="validationServer03" required="" <?php  echo 'value="'; if (isset($_GET['code'])){echo $_GET['code'];}; echo '"'; ?>>
            <div class="invalid-feedback">
              Please provide your invite code.
            </div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="button">Create Account</button>
			<button class="btn btn-secondary" type="button" onclick = "cancel()">Cancel</button>
          </div>
        </form>
        </div></div>

      </div>
		
     
	</body>
<html>