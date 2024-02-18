<?php
	session_start();
	if (!isset($_SESSION["role"])) {
		header('location: ./login.php');
		//die();
	} else if ($_SESSION["role"] == "Manager") {
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "border-bottom border-primary link-primary";
		include "./navbar_m.php";
		include "./dashboard_m.php";
		
	} else if ($_SESSION["role"] == "TL") {
		$topicview = "link-dark";
		$taskcreate = "link-dark";
		$taskview = "link-dark";
		$dashview = "border-bottom border-primary link-primary";
		
		try {
			include "db_connection.php";
			$conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
		} catch (PDOException $e) {
			echo "<script>alert('Failed to connect to database');</script>";
			exit();
		}
		$result = $conn->query("SELECT COUNT(project_ID) FROM project WHERE team_leader = ".$_SESSION["user_ID"]." GROUP BY team_leader;");
		$numOfProjectLeads = $result->fetch(PDO::FETCH_NUM)[0];
		
		include "./navbar_tl.php";
		if ($numOfProjectLeads == 0) {
			echo "<p style='text-align:center;margin: 5rem 2rem'>You currently do not lead any teams.</p>";
			include "./footer.php";
		} else if ($numOfProjectLeads == 1) {
			include "./view_team.php";
		} else {
			include "./view_many_teams_tl.php";
		}
	
	} else if ($_SESSION["role"] == "Employee") {
		$topicview = "link-dark";
		$dashview = "border-bottom border-primary link-primary";
		include "./navbar_e.php";
		include "./dashboard_e.php";
	}
?>