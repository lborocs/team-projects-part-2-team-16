<?php
	session_start();
	$ErrorMessage = "";
	$saved = "none";
	include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
	function refreshLinks($conn){
		mysqli_query($conn,"DELETE FROM activeInviteCodes WHERE expires < '".date("Y-m-d")."'");
		$sql = "SELECT code,expires
			FROM activeInviteCodes
			WHERE authorID = ".$_SESSION['user_ID']." ORDER BY expires DESC";
			$codeResult = mysqli_query($conn,$sql);

		if (!$codeResult) {
			echo "Connection Error.";
			exit;
		}
		return $codeResult;
	}

	//Generate new links
	function generateRandomCode() {
		$randomCode = '';
		for ($i = 0; $i < 4; $i++) {
			for ($j = 0; $j < 4; $j++) {
				$randomCode .= chr(rand(65, 90));
			}
			if ($i < 3) {
				$randomCode .= '-';
			}
		}
		return $randomCode;
	}
	function structure_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	  }
	if(!empty($_POST['lightSwitch'])) {
		$codeResult = refreshLinks($conn);
		if($_SESSION["lightmode"] == 1){
			$_SESSION["lightmode"] = 0;
			$sql = "UPDATE users SET lightmode = 0 WHERE user_ID ='".$_SESSION["user_ID"]."'";
			mysqli_query($conn,$sql);
		}else{
			$_SESSION["lightmode"] = 1;
			$sql = "UPDATE users SET lightmode = 1 WHERE user_ID ='".$_SESSION["user_ID"]."'";
			mysqli_query($conn,$sql);
		}
	}
	if (!empty($_POST['generateLink'])) {
		$code = generateRandomCode();
		$sql = "SELECT * FROM activeInviteCodes WHERE code = '$code'";
		while(mysqli_num_rows(mysqli_query($conn,$sql))!=0){
			$code = generateRandomCode();
			$sql = "SELECT * FROM activeInviteCodes WHERE code = '$code'";
		}
		$sql = "SELECT COUNT(code) AS code_count
				FROM activeInviteCodes
				WHERE authorID =".$_SESSION['user_ID'];
		$result = mysqli_query($conn,$sql);
		if(mysqli_fetch_assoc($result)['code_count'] <5){
			$expirationDate = date('Y-m-d', strtotime('+7 days'));
			$sql = "INSERT INTO activeInviteCodes VALUES('$code','$expirationDate',". $_SESSION['user_ID'].")";
			mysqli_query($conn,$sql);
		}
		$codeResult = refreshLinks($conn);
	}if(!empty($_POST['saveChanges'])){
		$saved = "none";
		$valid = true;
		$codeResult = refreshLinks($conn);
		$pass1 = structure_input($_POST["password1field"]);
    	$pass2 = structure_input($_POST["password2field"]);
		$iconSelection = $_POST["iconField"];
		if(($_SESSION["icon"] != $iconSelection)&&($iconSelection!='')){
			$_SESSION["icon"] = $iconSelection;
			$sql = "UPDATE users SET icon = '".$iconSelection."' WHERE user_ID ='".$_SESSION['user_ID']."'";
			mysqli_query($conn,$sql);
			$saved = "block";
            $ErrorMessage = '';
		}
		if(($pass1!='')|($pass2!='')){
			if($pass1 == $pass2){
				$sql = "SELECT forename,surname FROM users WHERE user_ID =".$_SESSION['user_ID'];
				$result = mysqli_query($conn,$sql);
				$row = mysqli_fetch_assoc($result);
				if(str_contains(strtolower($pass1),strtolower($row['forename']))){
					$valid = false;
					$ErrorMessage = "Password must not contain firstname or secondname.";
				}else if(str_contains(strtolower($pass1),strtolower($row['surname']))){
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
			if($valid){
				include "db_connection.php";
				$conn = mysqli_connect($servername, $username, $password, $dbname);
				if (!$conn) {
				  echo "Connection Error." ;
				  exit;
				}	
				$sql = "UPDATE users SET encrypted_pass ='".$encryptedPassword."' WHERE user_ID='".$_SESSION['user_ID']."'";
				mysqli_query($conn,$sql);
				$saved = "block";
            	$ErrorMessage = '';	
			}
		}
	}	
	//darkmode/lightmode css
	if($_SESSION["lightmode"] == 1){
		$colour = " text-light bg-dark";
	}if($_SESSION["lightmode"] != 1){
		$colour = " bg-white text-dark";
	}
	?>
	<script>
		function settings() {
			window.location.href = "./settings.php";
		};
		function logout() {
			window.location.href = "./login.php";
		};
		document.body.className = "<?php echo $colour;?>";
		document.documentElement.className = "<?php echo $colour;?>";
	</script>
	<div class="<?php if($_SESSION["lightmode"] == 1){echo 'bg-secondary text-light border-light border-bottom';}else{echo 'bg-dark text-light';}?>  px-4 py-5 text-center" style="margin:0px; padding:0px;">
		<div class="py-5">
			<h1 class="display-5 fw-bold ">Settings</h1>
		</div>
	</div>
	<div class="container <?php echo $colour;?>" style = "padding:4px 0px 0px 0px;">
	<div class="alert alert-success alert-dismissible fade show" role="alert" style = "display:<?php echo $saved; ?>;">
        Changes Saved.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:4px 4px 0px 4px;">
			<label for="websiteColour" class="form-label">Website Colour</label>
			<div class="mb-3 form-check form-switch">
			<input class="form-check-input" type="checkbox" role="switch" id="websiteColour"  onclick="refresh('lightSwitch')" <?php if ($_SESSION["lightmode"] == 1){echo "checked";}?>>
            <label class="form-check-label" for="websiteColour">Dark Mode</label>
          </div>
		</div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
			<label for="viewlinks" class="form-label">Invite New Employees</label><br>
			<button type="button" class="btn btn-outline-primary" id = "viewlinks" data-bs-toggle="modal" data-bs-target="#exampleModalCenteredScrollable">View Active Invite Links</button>
			<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModalDefault">Generate New Link</button>
		</div>
			<div class="col-md-12" style="padding:0px 4px;">
				<label for="password1field" class="form-label">New Password</label>
				<input type="password" class="form-control" id = "password1field" name="password1field" value="" >
			</div>
			<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
				<label for="password2field" class="form-label">Confirm New Password</label>
				<input type="password" id = "password2field" name="password2field" class="form-control" placeholder="" value="">
			</div>
			<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
				<label for="icon" class="form-label">Icon Colour</label>
				<select class="form-select" id ="iconField">
					<?php $colours = array("grey", "blue", "green", "red","pink","purple");
					echo "<option value=''>".$_SESSION["icon"]."</option>";
					foreach ($colours as $colour){
						if($colour != $_SESSION["icon"]){
							echo "<option>".$colour."</option>";
						}
					}?>
				</select>
			</div>
			<p style = "color:red;"><?php echo $ErrorMessage; ?></p>
		</div>
		<div class="container <?php echo $colour;?>">
			<div class="row">
				<div class="col-md-6" style="padding:1% 2%;">
					<button class="w-100 btn btn-primary btn-md" onclick="refresh('saveChanges')">Save Changes</button>
				</div>
				<div class="col-md-6" style="padding:1% 2%;">
					<button class="w-100 btn btn-secondary btn-md" type="submit" onclick="window.location.href = './dashboard.php';">Cancel</button>
				</div>
			</div>
		</div>
	<script>
	</script>
	<div class="modal fade" id="exampleModalCenteredScrollable" tabindex="-1" aria-labelledby="exampleModalCenteredScrollableTitle" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable text-dark">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalCenteredScrollableTitle">Your Active Invite Links</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<?php
			if (mysqli_num_rows($codeResult) == 0) {
				echo "No Active Invite Codes";
			}else{
				$n = 1;
				while($row = mysqli_fetch_assoc($codeResult)) { 
					echo "<h4>Link $n"; if(($n==1)&&(mysqli_num_rows($codeResult) != 1)){echo"(Most Recent)";} echo"</h4>";
					echo "<h5>Invite Link</h5>"; 
					echo "<p"; if($n ==1){echo " id='copy-invite'";}echo ">http://34.142.93.199/create.php?code=". $row['code']. "</p>"; 
					echo "<h5>Expiry Date</h5>"; 
					echo "<p>" . date("d/m/Y", strtotime($row['expires'])) . "</p><br>"; 
					$n=$n+1;
				} 
			}
			?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		</div>
		</div>
	</div>
	</div>
	<div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
	<div class="modal-dialog text-dark">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Generate New Link</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			Are you sure you wish to continue? <br> You can only have upto 5 invite links active at once.
			<br>Note: Once you reach your limit, you must wait for them to expire (7 days), or wait for an employee to use one.
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
			<button type="submit" class="btn btn-primary" data-bs-dismiss="modal" onclick="refresh('generateLink')">Generate New Link</button>
		</div>
		</div>
	</div>
	</div>
	<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top <?php echo $colour;?>"
		style="padding-left: 25px; padding-right: 25px;">
		<p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

		<a href="/"
			class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
			<img src="./logo.png" alt="mdo" width="200" height="50">
			</svg>
		</a>

		<div class="justify-content-end">
			<p>Phone: 01509 888999</p>
			<p>Email: king@make‐it‐all.co.uk</p>
		</div>
	</footer>
	<script>
	<?php 
		if($_SESSION["lightmode"] == 1){
			echo '
			var navbar = document.getElementsByClassName("settingsCSS");
			for(var i = 0; i < navbar.length; i++)
			{
				navbar[i].classList.remove("bg-white", "text-dark");
            	navbar[i].classList.add("text-light", "bg-dark");
			}';
		}if($_SESSION["lightmode"] != 1){
			echo 'var navbar = document.getElementsByClassName("settingsCSS");
			for(var i = 0; i < navbar.length; i++)
			{
				navbar[i].classList.add("bg-white", "text-dark");
           		navbar[i].classList.remove("text-light", "bg-dark");
			}';
		}echo 'document.getElementById("pageIcon").src = "./'.$_SESSION["icon"].'.png";';
	?></script>
