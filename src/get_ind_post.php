<?php
// get session variables and connect to db
session_start();
try {
	include "db_connection.php";
	$conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
	echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
	header("location: view_topics.php");
}

// if the user is trying to reply the post
if (isset($_POST["replyInput"])) {
	$result = $conn->query("SELECT max(response_ID) FROM response"); // gets an incremented ID for the new response
    $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
    if ($maxID == null) {
        $response_ID = 1;
    } else {
        $response_ID = $maxID[0] + 1;
    }

	$date = date("Y-m-d");
	$userID = intval($_SESSION["user_ID"]);
	if (isset($_GET["POST_ID"])) {
		if (is_numeric($_GET["POST_ID"])) {
			$postID = intval($_GET["POST_ID"]);
		} else {
			header("location: dashboard.php");
		}
	} else {
		header("location: dashboard.php");
	}

	// uses PDO prepare to coutneract sql injection attempts
	$replyQuery = $conn->prepare("INSERT INTO response (response_ID, user_ID, post_ID, content, Date) values (:response_ID, :user_ID, :post_ID, :content, Date :date)");
	$replyQuery->bindParam(":response_ID", $response_ID, PDO::PARAM_INT);
	$replyQuery->bindParam(":user_ID", $userID, PDO::PARAM_INT);
	$replyQuery->bindParam(":post_ID", $postID, PDO::PARAM_INT);
	$replyQuery->bindParam(":content", $_POST["replyInput"], PDO::PARAM_STR);
	$replyQuery->bindParam(":date", $date, PDO::PARAM_STR);
	$replyQuery->execute();
}

// if the user is trying to delete the post
if (isset($_POST['deleteID'])) {
	if (is_numeric($_POST['deleteID'])) {
		$ID = intval($_POST['deleteID']);
		$result = $conn->query("select * from posts where post_ID = $ID");
		$delpost = $result->fetch(PDO::FETCH_ASSOC);
		// ensures only managers and the origional poster can delete posts
		if ($delpost['user_ID'] == $_SESSION["user_ID"] || $_SESSION["role"] == "Manager"){
			$delQuery = $conn->query("delete from posts where post_ID = $ID;");
			$delQuery = $conn->query("delete from response where post_ID = $ID;");
			header("location: ./view_topics.php");
		}
	}
}

// deletes replys
if (isset($_POST['deleteRplyID'])) {
	if (is_numeric($_POST['deleteRplyID'])) {
		$rplyID = intval($_POST['deleteRplyID']);
		$result = $conn->query("select * from response where response_ID = $rplyID");
		$rply = $result->fetch(PDO::FETCH_ASSOC);
		// ensures only managers and the origional poster can delete posts
		if ($rply['user_ID'] == $_SESSION['user_ID'] || $_SESSION["role"] == "Manager") {
			$conn->query("delete from response where response_ID = $rplyID");
		}
	} 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Post</title>
	<link rel="icon" type="image/x-icon" href="./imgs/logo.ico">
	<meta name="theme-color" content="#7952b3">
	
	<style>
		.entry-box {
			background-color: white;
			border-bottom: 1px solid #a3a3a3;
			border-top: 1px solid #a3a3a3;
			padding: 0px 15px;
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
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

</head>

<script>
    <?php
    if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}
    ?>
    
    $(document).ready(function() {
        if ("<?php echo $colour ?>" == "text-light bg-dark") {
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        }
    })
</script>

<body>
	<!-- loads the header -->
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
		$taskview = "link-dark";
		include "./navbar_tl.php";
	} else if ($_SESSION["role"] == "Employee") {
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "link-dark";
		include "./navbar_e.php";
	} else {
		header("location: dashboard.php");
	}

	// checks if the post ID is set and then loads the post and reply from the DB
	if (isset($_GET["POST_ID"])) {
		if (is_numeric($_GET["POST_ID"])) {
			$ID = intval($_GET["POST_ID"]);
			// get the post
			$getPostQuery = $conn->query("select * from posts, users where post_ID = $ID and posts.user_ID = users.user_ID");
			$post = $getPostQuery->fetch(PDO::FETCH_ASSOC);

			// update views
			$newViews = $post['views'] + 1;
			$post['views'] ++;
			$updateSql = "UPDATE posts SET views = $newViews WHERE post_ID = $ID";
			$conn->query($updateSql);
			
			// get replies
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

	<div class="container" style="border-left: 1px solid #dee2e6;border-right: 1px solid #dee2e6;padding: 10px 20px;">
		<!-- back to posts button -->
		<button type="button" style="margin-top: 10px"class="btn btn-dark" onclick="window.location.href='view_posts.php?Post_topic_ID=<?php echo $post['topic_ID'];?>'">Back</button>
		<div class="container">
			<!-- post container -->
			<div class="row entry-box my-4">
				<!-- meta info row (user name, vierws, date, profile pic...) -->
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
				<!-- content row (text and image)-->
				<div class="row" style="border-bottom: 1px solid #ccc; padding-bottom: 11px;">
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
				// echos buttons for editing and deleting the post if the user is the origional poster or a manager
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
			// echos a div for each reply, with a button to delete the reply if the user is the origional poster or a manager
			foreach ($replies as $rply) {
				echo "<div class='row entry-box my-4 response-row'>";
				echo "<div class='col'>";
				echo "<p class='response-number'><small>".$rply["forename"]." ".$rply["surname"]."</small></p>";
				echo "<p>".$rply["content"]."</p>";
				
				if ($rply['user_ID'] == $_SESSION['user_ID'] || $_SESSION["role"] == "Manager") {
					$rplyID = $rply['response_ID'];
					echo "
					<form action='' method='post' id='delRplyForm'>
					<div class='row' style='border-top: 1px solid #ccc;'>
					<p style='color: #636b74; display: flex; justify-content: right;'>
					<span type='button' onclick='document.getElementById(\"delRplyForm\").submit()' style='margin-left: 10px;'>delete</span>
					</p>
					<input type='hidden' name='deleteRplyID' value=$rplyID>
					</div>
					</form>";
				}
				echo "</div></div>";
			}
			?>
			
			<!-- reply input -->
			<form action="" method="post">
				<div class="input-group">
					<input name="replyInput" type="text" class="form-control rounded" placeholder="Type reply here..." required 
					oninvalid="this.setCustomValidity('You must enter a response in order to post it')" oninput="this.setCustomValidity('')"/>
					<button name="postReply" type="submit" class="btn btn-outline-primary" value="1	">Reply</button>		
				</div>
			</form>


		</div>
	</div>
	<?php include "footer.php"; ?>
</body>

</html>
