<?php
/*Verifies inputs for adding a task and adds the task to the database, takes the user to dashboard if successful
POST data should have names: employee, title, description, date, manhours, (optional) project

PDO object $conn must be defined before including this file. A session must be created to get the user ID.

If the user is adding a task while viewing a team, $projectID must be defined:
$projectID : the current project ID if viewing a project
*/

function validate_title_desc($title=null, $desc=null) {
	if ($title == null) {
		if (isset($_POST["title"])) {		//checks if title is valid
			$title = $_POST["title"];
		} else {
			return [false, "Invalid task title, not set"];
		}
	}
	if (strlen($title) > 255) {
		return [false, "Invalid task title, too long"];
	}
	
	if ($desc == null) {
		if (isset($_POST["description"])) {				//checks if description is valid
			$desc = $_POST["description"];
		} else {
			return [false, "Invalid task description"];
		}
	}
	if (strlen($desc) > 1000) {
		return [false, "Invalid task description"];
	}
	
    return [true, ""];
}

function validate_project($projectID, $conn) {  //checks if project id is valid
    if ($projectID == null) {
        if (isset($_POST["project"])) {
            $projectID = $_POST["project"];
        } else {
            return [false, "Invalid project"];
        }
    } 
    			
    if (!is_numeric($projectID)) {
        return [false, "Invalid project"];
    } else {
        if ($_SESSION["role"] == "Manager") {
            return [true, ""];
        } else if ($_SESSION["role"] == "TL") {		//checks if team leader leads the input team
            $result = $conn->query("SELECT project_ID FROM project WHERE team_leader = ".$_SESSION["user_ID"].";");
            $projectsLeadByUser = $result->fetchAll(PDO::FETCH_NUM);
            foreach($projectsLeadByUser as $row) {
                if ($row[0] == $projectID) {
                    return [true, ""];
                }
            }
        }
        return [false, "Team leader does not have permission to make changes to given team"];
    }
    
	
}

function validate_date_time($date=null, $hours=null) {
	if ($date == null) {
		if (isset($_POST["date"])) {		//checks if due date is valid
			$date = $_POST["date"];
		} else {
			return [false, "Invalid due date"];
		}
	}
	if (!date_create_from_format("Y-m-d", $date)) {
		return [false, "Invalid due date"];
	}
	
	if ($hours == null) {
		if (isset($_POST["manhours"])) {	//checks if man hours of task is valid
			$hours = $_POST["manhours"];
		} else {
			return [false, "Invalid man hours for task"];
		}
	}
	if (!is_numeric($hours) && $hours > -1) {
		return [false, "Invalid man hours for task"];
	}else {
		return [true, ""];
	}
	
}

function validate_user($userID=null) {
	if ($userID == null) {
		if (isset($_POST["employee"])){		//checks if employee id is valid
			$userID = $_POST["date"];
		} else {
			return [false, "Invalid employee"];
		}
	}
	if (!is_numeric($userID)) {
		return "Invalid employee";
	} else {
		return [true, ""];
	}
}


function create_task($projectID,$conn) {
    // check will always be an array will 2 elements, a bool for if the entry was valid and a string with an error message or empty string if entry is valid
    $check = validate_user();
	if (!$check[0]) {
        echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
        return;
    }
	$empID = intval($_POST["employee"]);

    $check = validate_date_time();
	if (!$check[0]) {
        echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
        return;
    }
    $date = $_POST["date"];
    $hours = intval($_POST["manhours"]);

    $check = validate_project($projectID, $conn);
	if (!$check[0]) {
        echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
        return;
    }
	$projectID = intval($projectID ?? $_POST['project']);

    $check = validate_title_desc();
	if (!$check[0]) {
        echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
        return;
    }
	$title = $_POST["title"];
    $description = $_POST["description"];


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
		return;
	} else {
		echo "<script>alert('request unsucessful');</script>";
	}
}

?>
