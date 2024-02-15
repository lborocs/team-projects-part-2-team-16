<?php
/*Verifies inputs for adding a task and adds the task to the database, takes the user to dashboard if successful
POST data should have names: employee, title, description, date, manhours, (optional) project

PDO object $conn must be defined before including this file. A session must be created to get the user ID.

If the user is adding a task while viewing a team, $projectID must be defined:
$projectID : the current project ID if viewing a project
*/

function create_task($projectID,$conn) {
	if (isset($_POST["employee"])){		//checks if employee id is valid
		$empID = $_POST["employee"];
		if (!is_numeric($empID)) {
			return "Invalid employee";
		} else {
			$empID = intval($empID);
		}
	} else {
		return "Invalid employee";
	}
	if ($projectID == null) {
		if (isset($_POST["project"])) {			//checks if project id is valid
			$projectID = $_POST["project"];
			if (!is_numeric($projectID)) {
				return "Invalid project";
			} else {
				if ($_SESSION["role"] == "Manager") {
					$projectID = intval($projectID);
				} else if ($_SESSION["role"] == "TL") {
					$result = $conn->query("SELECT project_ID FROM project WHERE team_leader = ".$_SESSION["user_ID"].";");
					$projectsLeadByUser = $result->fetchAll(PDO::FETCH_NUM);
					//for each project thats returned, check if at least one of these projects is equal to $projectID
					//james double check this part because i am so tired and i dont know if this will work
					$projectID = intval($projectID);
				}
			}
		} else {
			return "Invalid project";
		}
	}
	if (isset($_POST["title"])){		//checks if title is valid
		$title = $_POST["title"];
		if (strlen($title) > 255) {
			return "Invalid task title";
		}
	} else {
		return "Invalid task title";
	}
	if (isset($_POST["description"])) {				//checks if description is valid
		$description = $_POST["description"];
		if (strlen($description) > 1000) {
			return "Invalid task description";
		}
	} else {
		return "Invalid task description";
	}
	if (isset($_POST["date"])) {		//checks if due date is valid
		$date = $_POST["date"];
		if (!date_create_from_format("Y-m-d", $date)) {
			return "Invalid due date";
		}
	} else {
		$errorMsg = "Invalid due date";
		return "Invalid due date";
	}
	if (isset($_POST["manhours"])) {	//checks if man hours of task is valid
		$hours = $_POST["manhours"];
		if (!is_numeric($hours) and $hours > -1) {
			return "Invalid man hours for task";
		}else {
			$hours = intval($hours);
		}
	} else {
		$errorMsg = "Invalid man hours for task";
		return "Invalid man hours for task";
	}
	//get a unique id for task
	$result = $conn->query("SELECT max(task_ID) FROM tasks");
	$maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
	if ($maxID == null) {
		$ID = 1;
	} else {
		$ID = $maxID[0] + 1;
	}
	
	//adds task to database
	$stmt = $conn->prepare("INSERT into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours, progress) 
			VALUES (:ID, :empID, :projectID, :title, :description, DATE :date, :hours, 0)");
	$stmt->bindParam(':ID', $ID, PDO::PARAM_INT);
	$stmt->bindParam(':empID', $empID, PDO::PARAM_INT);
	$stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
	$stmt->bindParam(':title', $title, PDO::PARAM_STR);
	$stmt->bindParam(':description', $description, PDO::PARAM_STR);
	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
	$stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
	
	if ($stmt->execute())  {
		header("location: dashboard.php");
		die();
	} else {
		echo "<script>alert('request unsucessful');</script>";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$projectID = $projectID ?? null;
	$errorMsg = create_task($projectID,$conn);
	if (!$errorMsg == "") {
		echo "<script>alert('Failed to create the task. One of your input violated input requirments: $errorMsg');</script>";
	}
}
?>