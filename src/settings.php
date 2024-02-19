<!--This page works asynchronously with settingsAsync.php to provide the ability of account modificaiton to each user.
In this page, a user can select from pre-set icons, change their password and choose between light and dark mode.

Also, this page is responsible for the generation and viwing of personal invite codes, which can be given to users who want
to join the system-->
<?php
	// start session
	session_start();
	$ErrorMessage = "";
	$saved = "none"; //do not display saving confirmation
	include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
	function refreshLinks($conn){
		mysqli_query($conn,"DELETE FROM activeInviteCodes WHERE expires < '".date("Y-m-d")."'"); // clear all invalid invite codes
		$sql = "SELECT code,expires
			FROM activeInviteCodes
			WHERE authorID = ".$_SESSION['user_ID']." ORDER BY expires DESC"; // select users current invide codes and expiration dates
			$codeResult = mysqli_query($conn,$sql);

		if (!$codeResult) {
			echo "Connection Error.";
			exit;
		}
		return $codeResult;
	}
	//Generate new links
	function generateRandomCode() {
		//creates a new invite code in correct format (26^16 possible combinations)
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
		//formats data for processing
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	  }
	
	$codeResult = refreshLinks($conn);//refhreshes the links displayed in the modal
	//darkmode/lightmode css
	if($_SESSION["lightmode"] == 1){
		$colour = " text-light bg-dark";
	}if($_SESSION["lightmode"] != 1){
		$colour = " bg-white";
	}
	?>
	
<html class = "<?php echo $colour;?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="./imgs/logo.ico">
	<title>Settings</title>
</head>

<body class = "<?php echo $colour;?>";>
<!--the following code loads the navbar onto the page, and sets the css values accordingly to display that we arent on any of the main headers-->
<div style="margin:0px; padding:0px;">
		<?php 
			if(!isset($_SESSION["role"])){
				echo "<script>window.location.href='./login.php'</script>";
			}else if($_SESSION["role"] == "Manager"){
				$taskcreate = "link-dark";
				$topicview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_m.php";
			}else if($_SESSION["role"] == "TL"){
				$topicview = "link-dark";
				$taskcreate = "link-dark";
				$taskview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_tl.php";
			}else if($_SESSION["role"] == "Employee"){
				$topicview = "link-dark";
				$dashview = "link-dark";
				include "./navbar_e.php";
			}
		?>
	</div>
	<div id = 'everything'>
	<!-- The everything div, alike other pages, is used to display html returned via the ajax request-->
	<script>
		function settings() {
			window.location.href = "./settings.php";
		};
		function logout() {
			window.location.href = "./login.php";
		};
	</script>
	<!-- The following echo is for light or dark mode, same as the $colour variable -->
	<div class="<?php if($_SESSION["lightmode"] == 1){echo 'bg-secondary text-light border-light border-bottom';}else{echo 'bg-dark text-light';}?>  px-4 py-5 text-center" style="margin:0px; padding:0px;">
		<div class="py-5">
			<h1 class="display-5 fw-bold ">Settings</h1>
		</div>
	</div>
	<div class="container <?php echo $colour;?>" style = "padding:4px 0px 0px 0px;">
	<!-- The $saved variable is used to show or hide the saved alert. Default is to hide when loaded.-->
	<div class="alert alert-success alert-dismissible fade show" role="alert" style = "display:<?php echo $saved; ?>;">
        Changes Saved.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:4px 4px 0px 4px;">
			<label for="websiteColour" class="form-label">Website Colour</label>
			<div class="mb-3 form-check form-switch">
			<!-- The lightSwitch variation of a refresh execution is carried out when the switch has been pressed. Changes saved automatically.-->
			<input class="form-check-input" type="checkbox" role="switch" id="websiteColour"  onclick="refresh('lightSwitch')" <?php if ($_SESSION["lightmode"] == 1){echo "checked";}?>>
            <label class="form-check-label" for="websiteColour">Dark Mode</label>
          </div>
		</div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
			<!-- Used to activate corresponding modals -->
			<label for="viewlinks" class="form-label">Invite New Employees</label><br>
			<button type="button" class="btn btn-outline-primary" id = "viewlinks" data-bs-toggle="modal" data-bs-target="#exampleModalCenteredScrollable">View Active Invite Links</button>
			<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModalDefault">Generate New Link</button>
		</div>
			<div class="col-md-12" style="padding:0px 4px;">
				<label for="password1field" class="form-label">New Password</label>
				<input type="password" class="form-control" id = "password1field" name="password1field" >
			</div>
			<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
				<label for="password2field" class="form-label">Confirm New Password</label>
				<input type="password" id = "password2field" name="password2field" class="form-control" placeholder="">
			</div>
			<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
				<label for="icon" class="form-label">Icon Colour</label>
				<select class="form-select" id ="iconField">
					<!--Generates a dropdown selection of all posible icon colours for user customisation-->
					<?php $colours = array("grey", "blue", "green", "red","pink","purple");
					echo "<option value=''>".$_SESSION["icon"]."</option>";
					foreach ($colours as $colour){
						if($colour != $_SESSION["icon"]){
							echo "<option>".$colour."</option>";
						}
					}?>
				</select>
			</div>
			<!-- Error message is used to communicate to the user when a problem has occured, such as invalid password change. -->
			<p style = "color:red;"><?php echo $ErrorMessage; ?></p>
		</div>
		<div class="container <?php echo $colour;?>">
			<div class="row">
				<div class="col-md-6" style="padding:1% 2%;">
					<!-- Attempts to save any changes user has made -->
					<button class="w-100 btn btn-primary btn-md" onclick="refresh('saveChanges')">Save Changes</button>
				</div>
				<div class="col-md-6" style="padding:1% 2%;">
					<button class="w-100 btn btn-secondary btn-md" type="submit" onclick="window.location.href = './dashboard.php';">Cancel</button>
				</div>
			</div>
		</div>
	<script>
	</script>
	<!--This modal is used to draw the users attention to the active invite links, used to invite people to the system -->
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
				//informs the user if they have no valid invite codes currently asssigned to their account
				echo "No Active Invite Codes";
			}else{
				$n = 1;
				//the following formats the invite link to work correctly with the get function of the create.php page
				//also formats date to be presented in british format
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
			<!-- The following is used to draw the users attention to whether or not they want to generate a new link.
			We double check this as if the user doesnt acutally want to create a new link then it would be more space efficient for the database
			to not have to store a link through to expiry. -->
			<h5 class="modal-title" id="exampleModalLabel">Generate New Link</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			Are you sure you wish to continue? <br> You can only have upto 5 invite links active at once.
			<br>Please Note: Once you reach your limit, you must wait for them to expire (7 days), or have an employee use one.
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
			<!-- If user is certain they wish to generate a link, then thre refresh function is then activated on the generate link mode. -->
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
			<img src="./imgs/logo.png" alt="mdo" width="200" height="50">
			</svg>
		</a>

		<div class="justify-content-end">
			<p>Phone: 01509 888999</p>
			<p>Email: king@make‐it‐all.co.uk</p>
		</div>
	</footer>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
		integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	
	<script>
		function refresh(typeofsubmit) {
			//this function is used to save and activate any changes a user may have made.
			// this includes generating a new link, saving changes or changing between colour modes.
			var data = {};
			if(typeofsubmit == "generateLink"){
				//if the user wants to generate a new link then send post data to settingsAsync accordingly
				data.generateLink ='generateLink';
			}else if(typeofsubmit == "saveChanges"){
				//if user wants to save, then sen the values of the icon,passsword1 and password 2 fields to settingsAsync
				data.saveChanges ='saveChanges';
				data.password1field = document.getElementById('password1field').value;
				data.password2field = document.getElementById('password2field').value;
				data.iconField = document.getElementById('iconField').selectedOptions[0].value;
			}else if(typeofsubmit == "lightSwitch"){
				//if the user presses the lightswitch then send data to settingsAsync accordingly
				data.lightSwitch ='lightSwitch';
			}
			$.ajax({
				url: 'settingsAsync.php',
				method: 'POST',
				data: data,
				success: function(response) {
					// Handle success
					$('#everything').html(response); // Update displayed content
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error); // Log error to the console
				}
			});

		}
	</script>
</body>
</html>
