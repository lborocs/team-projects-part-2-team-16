<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
	integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
	integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
	crossorigin="anonymous"></script>

<head>
	<link rel="icon" type="image/x-icon" href="./logo.ico">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Team Leaders Dash</title>

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


<body style = "margin:0px; padding:0px;">
<?php session_start();
	if($_SESSION["expiry"] >= date('m-d-Y')){
		echo "<script>window.location.href='./login.php'</script>";
	}
	if(!isset($_SESSION["user_ID"])){
		echo "<script>window.location.href='./login.php'</script>";
	}
	if($_SESSION["role"] != "TL"){
		echo "<script>window.location.href='./login.php'</script>";
	}
	if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}?>
	<script>
		function logout() {
			window.location.href = "./login.php";
		};
	</script>
	<header class="p-3 border-bottom <?php echo $colour;?>" style = "margin:0px; padding:0px;">
		<div class="container <?php echo $colour;?>">
			<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start <?php echo $colour;?>">

				<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
					<li><a href="./dashboard.php" class="nav-link px-2 <?php if($dashview=="link-dark"){echo $colour;} echo $dashview;?>">Dashboard</a></li>
					<li><a href="./view_topics_tl.php" class="nav-link px-2 <?php if($topicview=="link-dark"){echo $colour;} echo $topicview;?>">Topics</a></li>
					<li><a href="./create_task_tl.php" class="nav-link px-2 <?php if($taskcreate=="link-dark"){echo $colour;} echo $taskcreate;?>">Assign Tasks</a></li>
					<li><a href="./view_tasks_tl.php" class="nav-link px-2  <?php if($taskview=="link-dark"){echo $colour;} echo $taskview;?>">View Tasks</a></li>
				</ul>

				<form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" action="./view_topics_tl.php">
					<input type="search" class="form-control" placeholder="Search Topics" aria-label="Search">
				</form>

				<div class="dropdown text-end">
					<a href="#" class="d-block link-dark text-decoration-none dropdown-toggle <?php echo $colour;?>" id="dropdownUser1"
						data-bs-toggle="dropdown" aria-expanded="false">
						<img src="./<?php echo $_SESSION["icon"];?>.png" alt="mdo" width="32" height="32" class="rounded-circle">
					</a>
					<ul class="dropdown-menu text-small <?php echo $colour;?>" aria-labelledby="dropdownUser1">
						<li><a class="dropdown-item <?php echo $colour;?>" href="./create_topic_tl.php">Create New Topic...</a></li>
						<li><a class="dropdown-item <?php echo $colour;?>" href="./settings.php">Settings</a></li>
						<li>
							<hr class="dropdown-divider <?php echo $colour;?>">
						</li>
						<li><a class="dropdown-item <?php echo $colour;?>" href="#" onclick="logout()">Sign out</a></li>
					</ul>
				</div>
			</div>
		</div>
	</header>
</body>
