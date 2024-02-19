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
		$taskview = "border-bottom border-primary link-primary";
		$dashview = "link-dark";
		include "./navbar_tl.php";
		include "./dashboard_e.php";
	} else if ($_SESSION["role"] == "Employee") {
		$topicview = "link-dark";
		$dashview = "border-bottom border-primary link-primary";
		include "./navbar_e.php";
		include "./dashboard_e.php";
	}
	include "./footer.php";
?>