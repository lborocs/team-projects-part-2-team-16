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
			echo "Team leader leads no teams, not yet implemented. User ID: ".$_SESSION["user_ID"];
		} else if ($numOfProjectLeads == 1) {
			include "./view_team_tl.php";
		} else {
			echo "Team leader leads ".$numOfProjectLeads." teams, not yet implemented. User ID: ".$_SESSION["user_ID"];
			exit;
		}
		include "./footer.php";
	
	} else if ($_SESSION["role"] == "Employee") {
		$topicview = "link-dark";
		$dashview = "border-bottom border-primary link-primary";
		include "./navbar_e.php";
		include "./dashboard_e.php";
	}
?>