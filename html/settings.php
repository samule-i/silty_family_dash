<?php 
$table = "home";
require "lib/password.php";
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
if(isset($_POST["current_password"])){
	$db = new sqlite3('../main.db');
	$username = "'" . $_SESSION["username"] . "'"; 
	$Sqlite3Result = $db->query("SELECT password FROM users WHERE username = $username");
	if(!$Sqlite3Result){
		echo "error with Sqlite3Result";
		exit();
	}
	while($row = $Sqlite3Result->fetchArray(SQLITE3_ASSOC)){
		if(password_verify($_POST['current_password'], $row['password']) && ($_POST['new_password'] == $_POST['confirm_password']) && !($_POST['new_password'] == '')){
			$new_password = "'" . password_hash($_POST['new_password'], PASSWORD_DEFAULT) . "'";
			$Sqlite3Return = $db->exec("UPDATE users SET password = $new_password WHERE username = $username");
		}
	}
	$db->close();
}

if(isset($_POST["new_username"])){
	$db = new sqlite3('../main.db');
	$username = "'" . $_SESSION["username"] . "'";
	$new_username = "'".$_POST["new_username"]."'";
	$Sqlite3Result =  $db->query("SELECT password FROM users WHERE username = $username");
	while($row = $Sqlite3Result->fetchArray(SQLITE3_ASSOC)){
		if(password_verify($_POST['password'], $row['password']) && ($_POST['new_username'] == $_POST['confirm_username']) && !($_POST['new_username'] == '')){
			$Sqlite3Return = $db->exec("UPDATE users SET username = $new_username WHERE username = $username");
		}
	}
}

?>
<html>
<?php 
doctype();
head(); 
?>
<body>
<div class='container'>
<?php
html_header($table);
navigation();
?>
<div class="main">
<div class="content">
<?php
echo "<h1>Welcome " . $_SESSION["username"] . "</h1>";
?>

<h1>user controls</h1>
<h2>Change Username</h2>
<form name="change_username" action="settings.php" method="post">
<label for="new_username">New username:</label>
<input type="text" name="new_username">
<br />
<label for="confirm_username">Confirm username:</label>
<input type="text" name="confirm_username">
<br />
<label for="password">Password:</label>
<input type="password" name="password">
<br>
<input type="submit" value="submit">
</form>
<h2>Change Password</h2>
<form name="change_password" action="settings.php" method="post">
<label for="password">Current Password:</label>
<input type="password" name="current_password">
<br>
<label for="new_password">New Password:</label>
<input type="password" name="new_password">
<br>
<label for="confirm_password">Confirm Password:</label>
<input type="password" name="confirm_password">
<br>
<input type="submit" value="submit">
</form>
</div>
<?php
sidenav()
?>
<div class="clearer"><span></span></div>
</div>
<?php footer(); ?>
</div>
</body>
</html>