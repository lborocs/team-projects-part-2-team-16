<?php
session_start();
try {
	include "db_connection.php";
	$conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
	echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
	header("location: view_topics.php");
}

if (isset($_POST["replyInput"])) {
	

	$result = $conn->query("SELECT max(response_ID) FROM response");
    $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
    if ($maxID == null) {
        $response_ID = 1;
    } else {
        $response_ID = $maxID[0] + 1;
    }
	$date = date("Y-m-d");
	$userID = intval($_SESSION["user_ID"]);
	$postID = intval($_GET["POST_ID"]);

	$replyQuery = $conn->prepare("INSERT INTO response (response_ID, user_ID, post_ID, content, Date) values (:response_ID, :user_ID, :post_ID, :content, Date :date)");
	$replyQuery->bindParam(":response_ID", $response_ID, PDO::PARAM_INT);
	$replyQuery->bindParam(":user_ID", $userID, PDO::PARAM_INT);
	$replyQuery->bindParam(":post_ID", $postID, PDO::PARAM_INT);
	$replyQuery->bindParam(":content", $_POST["replyInput"], PDO::PARAM_STR);
	$replyQuery->bindParam(":date", $date, PDO::PARAM_STR);
	$replyQuery->execute();
}
if (isset($_POST['deleteID'])) {
	$ID = intval($_POST['deleteID']);
	$delQuery = $conn->prepare("delete from posts where post_ID = :post_ID;");
	$delQuery->bindParam(":post_ID", $ID, PDO::PARAM_INT);
	$delQuery->execute();
	$delQuery = $conn->prepare("delete from response where post_ID = :post_ID;");
	$delQuery->bindParam(":post_ID", $ID, PDO::PARAM_INT);
	$delQuery->execute();
	header("location: ./view_topics.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Post</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-T3c6CoIi6uLrA9TneNEoa7Rxnatzjc6, DSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
	<style>
		.entry-box {
			background-color: white;
			border: 1px solid #000;
			border-radius: 15px;
			padding: 10px 15px;
		}

		.row-tenth-height {
			height: 10vh;
		}

		.response-number {
			border-bottom: 1px solid #ccc;
		}

		.question {
			border-bottom: 1px solid #ccc;
			background-color: rgba(227, 207, 207, 0.303);
		}

		.response-row {
			margin-left: 20%;
		}

		.image-container {
			max-width: 100%;
			max-height: 100%;
			/* Set the container to be half the size */
		}

		.vertical-line {
			border-right: 1px solid #ccc;
			/* Add a vertical line on the right side */
		}

		.max-width-100 {
			max-width: 100%;
		}

		.max-height-100 {
			max-height: 18vh;
		}
	</style>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Post</title>
		<link rel="icon" type="image/x-icon" href="./logo.ico">
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
	</head>

</head>

<body>
	<?php
	if (!isset($_SESSION["role"])) {
		echo "<script>window.location.href='./login.php'</script>";
	} else if ($_SESSION["role"] == "Manager") {
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "link-dark";
		include "./navbar_m.php";
	} else if ($_SESSION["role"] == "TL") {
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "link-dark";
		include "./navbar_tl.php";
	} else if ($_SESSION["role"] == "Employee") {
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "link-dark";
		include "./navbar_e.php";
	} else {
		header("location: dashboard.php");
	}

	if (isset($_GET["POST_ID"])) {
		if (is_numeric($_GET["POST_ID"])) {
			$ID = intval($_GET["POST_ID"]);
			$getPostQuery = $conn->prepare("select * from posts, users where post_ID = :post_ID and posts.user_ID = users.user_ID");
			$getPostQuery->bindParam(":post_ID", $ID, PDO::PARAM_INT);
			$getPostQuery->execute();
			$post = $getPostQuery->fetch(PDO::FETCH_ASSOC);

			$getRepliesQuery = $conn->prepare("select * from response, users where post_ID = :post_ID and response.user_ID = users.user_ID order by response.Date DESC");
			$getRepliesQuery->bindParam(":post_ID", $ID, PDO::PARAM_INT);
			$getRepliesQuery->execute();
			$replies = $getRepliesQuery->fetchAll(PDO::FETCH_ASSOC);
		} else {
			header("location: view_topics.php");
		}
	} else {
		header("location: view_topics.php");
	}
	?>

	<div class="container" style="height: 10vh;">
		<button type="button" style="margin-top: 10px"class="btn btn-dark" onclick="window.location.href='view_posts.php?Post_topic_ID=<?php echo $post['topic_ID'];?>;'">Back</button>
		<div class="container">
			<div class="row entry-box my-4">
				<div class="row" style="border-bottom: 1px solid #ccc; margin-bottom: 10px">
					<div class="col-1 vertical-line d-flex justify-content-center align-items-center">
						<img src=<?php echo "./" . $post["icon"] . ".png";?> alt="User Icon" class="img-fluid max-width-100 max-height-100 rounded-circle" style="margin: 5px;">
					</div>
					<div class="col">
						<p class="response-number" style="margin-bottom: auto; font-size:x-large"><b>
							<?php echo $post["title"] ?>
						</b></p>
						<p><small>
							<span style="color: #636b74">Posted by: </span>
							<?php echo $post["forename"] . " " . $post["surname"] ?>
							<span style="color: #636b74; margin-left: 5px;"> Date Posted: </span>
							<?php echo $post["Date"] ?>
							<span style="color: #636b74; margin-left: 5px;"> Viewed </span>
							<?php echo $post["views"] ?> times
						</small></p>
					</div>
				</div>
				<div class="row" style="border-bottom: 1px solid #ccc">
					<?php
					if ($post["img_url"] != null && $post["img_url"] != "null") {
						echo "<p class='col-sm-6 col-12 vertical-line'>".$post["content"]."</p>";
						echo "<img src=".$post["img_url"]." alt='failed to load image' class='col-sm-6 col-10' style='margin:auto'>";
					} else {
						echo "<p>".$post["content"]."</p>";
					}
					?>
				</div>

				<?php
				if ($_SESSION["role"] == "Manager" || $_SESSION["user_ID"] == $post["user_ID"]) {
					$postID = $post["post_ID"];
					echo "
					<form action='' method='post' id='delForm'>
					<div class='row'>
					<p style='color: #636b74; display: flex; justify-content: right;'>
					<span type='button' style='margin-left: 10px;' onclick='window.location.href=\"./create_post.php?edit_ID=$postID\"'>edit</span>
					<span type='button' onclick='document.getElementById(\"delForm\").submit()' style='margin-left: 10px;'>delete</span>
					</p>
					<input type='hidden' name='deleteID' value=$postID>
					</div>
					</form>";
				}
				?>
			</div>
			<hr>
			<h2><?php echo count($replies) ?> Replies</h2>

			<?php
			foreach ($replies as $rply) {
				echo "<div class='row entry-box my-4 response-row'>";
				echo "<div class='col'>";
				echo "<p class='response-number'><small>".$rply["forename"]." ".$rply["surname"]."</small></p>";
				echo "<p>".$rply["content"]."</p>";
				echo "</div></div>";
			}
			?>
			
			<form action="" method="post">
				<div class="input-group">
					<input name="replyInput" type="text" class="form-control rounded" placeholder="Type reply here..." required 
					oninvalid="this.setCustomValidity('You must enter a response in order to post it')" oninput="this.setCustomValidity('')"/>
					<button name="postReply" type="submit" class="btn btn-outline-primary" value="1	">Reply</button>		
				</div>
			</form>


		</div>
		<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"
			style="padding-left: 25px; padding-right: 25px;">
			<p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

			<a href="/"
				class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
				<img src="logo.png" alt="mdo" width="200" height="50">
				</svg>
			</a>

			<div class="justify-content-end">
				<p>Phone: 01509 888999</p>
				<p>Email: king@make‐it‐all.co.uk</p>
			</div>
		</footer>
	</div>
</body>

</html>
