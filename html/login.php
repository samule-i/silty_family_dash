<?php
require "lib/password.php";
session_start();
if(isset($_POST["username"])){
	$username = "'" . $_POST["username"] . "'";
}
if(isset($_POST["password"])){
	$db = new sqlite3('../main.db');
	$prepare = $db->prepare("SELECT * FROM users WHERE username = :username");
    $prepare->bindparam(':username', htmlspecialchars($_POST["username"]));
    $result = $prepare->execute();
	if(!result){
		header("location:login.php");
		exit();
	}
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		if(password_verify(htmlspecialchars($_POST["password"]), $row['password'])){
			$_SESSION["username"] = $row["username"];
			$_SESSION["user_id"] = $row["id"];
			header("location: index.php");
			exit();
		}
	}
	$db->close();
}
?>
<html>
	<head>
		<title>
		ironserver
		</title>
		<Meta http-equiv="Content-type" content="Text/HTML; charset=iso-8859-1">
		<link type="text/css" rel="stylesheet" href="css/style.css" media="screen">
	</head>
	<body>
		<div class="login">
			<h1>Silty login:</h1>
			<form name="login" action="login.php" method="post">
			<label for="username">username:</label>
			<input type="text" name="username">
			<br>
			<label for="password">password:</label>
			<input type="password" name="password">
			<input type="submit" value="submit">
		</form>
		</div>
	</body>
</html>
