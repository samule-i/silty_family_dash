<?php
if(!isset($_POST['action'])){
	header("location: index.php");
	exit();
}

function escapechars($string){
	$string = str_replace("'", "''", $string);
	$string = str_replace("<br>", "", $string);
	$string = str_replace("<br><br>", "<br>", $string);
	return($string);
	
}

switch($_POST['action']){
	case "create":
		unset($_POST['action']);
		if(isset($_POST["table"])){
			$table = $_POST["table"];
			unset($_POST["table"]);
		} else {
			echo "no table set";
			exit();
		}
		foreach($_POST as $field => $x){
			if(!isset($fields)){
				$fields = $field;
			} else {
				$fields = $fields . ", " . $field;
			}
			$values = nl2br($values . ", '" . escapechars($x) . "'");
		}
		$values = substr($values, 1);
		$db = new sqlite3('../../main.db');
		$return = $db->exec("INSERT INTO $table($fields) VALUES($values)");
		if(!$return){
			echo "error<br>";
			echo $fields . " - " . $values . "<br>";
			echo $db->lastErrorMsg();
			exit();
		}
		$db->close();
		header("location: ../" . $table . ".php");
		exit();
		break;
	case "edit":
		unset($_POST["action"]);
		if(isset($_POST["table"])){
			$table = $_POST["table"];
			unset($_POST["table"]);
		} else {
			echo "no table set";
			exit();
		}
			if(isset($_POST["id"])){
			$id = $_POST["id"];
			unset($_POST["id"]);
		} else {
			echo "no id set";
		}
		foreach($_POST as $key => $value){
			if(!$values){
				$values = nl2br($key . " = '" . escapechars($value) . "'");
			} else {
				$values =  $values . ", " . nl2br($key . " = '" . escapechars($value) . "'");
			}
		}
		echo $values;
		$db = new sqlite3('../silty_ui/main.db');
		$return = $db->exec("UPDATE $table SET $values WHERE id = $id");
		if(!$return){
			echo "<br> error <br>";
			echo $db->lastErrorMsg();
			exit();
		}
		$db->close();
		header("location: ../" . $table . ".php");
		exit();
		break;
	case "delete":
		if(isset($_POST["table"])){
			$table = $_POST["table"];
		} else {
			echo "no table field found";
			exit();
		}
		if(isset($_POST["id"])){
			$id = $_POST["id"];
		} else{
			echo "no id field found";
			exit();
		}
		$db = new sqlite3('../silty_ui/main.db');
		$return = $db->exec("DELETE FROM $table WHERE id = $id");
		if(!$return){
			echo "<br> error <br>";
			echo $db->lastErrorMsg();
			exit();
		}
		$db->close();
		header("location: ../" . $table . ".php");
		exit();
		break;
}

?>