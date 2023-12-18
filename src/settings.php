<?php
	include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
	session_start();
	if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}
?>
<html class = "<?php echo $colour;?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Settings</title>
</head>

<body class = "<?php echo $colour;?>";>
	<script>
		function settings() {
			window.location.href = "./settings.php";
		};
		function logout() {
			window.location.href = "./login.php";
		};
	</script>
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
	<div class="<?php if($_SESSION["lightmode"] == 1){echo 'bg-secondary text-light';}else{echo 'bg-dark text-light';}?>  px-4 py-5 text-center" style="margin:0px; padding:0px;">
		<div class="py-5">
			<h1 class="display-5 fw-bold">Settings</h1>
		</div>
	</div>
	<div class="container <?php echo $colour;?>">
		<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
			<label for="websiteColour" class="form-label">Website Colour</label>
			<div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="websiteColour" <?php if ($_SESSION["lightmode"] == 1){echo "checked";}?>>
            <label class="form-check-label" for="websiteColour">Dark Mode</label>
          </div>
		</div>
		<div class="col-md-12" style="padding:0px 4px;">
			<label for="p1" class="form-label">Password</label>
			<input type="password" class="form-control" id="p1" value="" required="">
		</div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
			<label for="p2" class="form-label">Confirm Password</label>
			<input type="password" class="form-control" id="p2" placeholder="" value="" required="">
		</div>
		<div class="col-md-12 <?php echo $colour;?>" style="padding:0px 4px;">
			<label for="icon" class="form-label">Icon Colour</label>
			<select class="form-select" id="icon" required="">
				<?php $colours = array("grey", "blue", "green", "red","pink","purple");
				echo "<option value=''>".$_SESSION["icon"]."</option>";
				foreach ($colours as $colour){
					if($colour != $_SESSION["icon"]){
						echo "<option>".$colour."</option>";
					}
				}?>
			</select>
		</div>
		<div class="row <?php echo $colour;?> align-items-end" style="padding:0px 4px;">
			<div class="col-md-8" style="">
				<label for="copy-invite" class="form-label">Invite Link</label>
				<input type="text" class="form-control" id="copy-invite" value="http://34.142.93.199/create.php/?code=<?php echo $code;?>" readonly>
			</div>
			<div class="col-md-4" style="">
				<button class="btn btn-primary" id="copy-invite-button">Copy Link</button>
				<button class="btn btn-secondary" id="copy-invite-button">Refresh Link</button>
			</div>
		</div>
	</div>
	<div class="container <?php echo $colour;?>">
		<div class="row">
			<div class="col-md-6" style="padding:1% 2%;">
				<button class="w-100 btn btn-primary btn-md" type="submit">Save Changes</button>
			</div>
			<div class="col-md-6" style="padding:1% 2%;">
				<button class="w-100 btn btn-secondary btn-md" type="submit" onclick="window.location.href = './dashboard.php';">Cancel</button>
			</div>
		</div>
	</div>
	<script>
		document.getElementById("copy-invite-button").addEventListener("click", function () {
			var copyText = document.getElementById("copy-invite");
			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand("copy");
			alert("Copied.");
		});
	</script>
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
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
		integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
</body>
<html>
