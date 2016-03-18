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

if(isset($_POST["add_user_username"])){
	if($_POST["add_user_username"] == $_POST["confirm_add_user_username"] && !(empty($_POST["add_user_username"]))){
		$new_user = "'".$_POST["add_user_username"]."'";
	} else {
		echo "username error";
		return;
	}
	if($_POST["add_user_password"] == $_POST["confirm_add_user_password"] && !(empty($_POST["add_user_password"]))){
		$new_password = "'".password_hash($_POST["add_user_password"], PASSWORD_DEFAULT)."'";
	} else {
		echo "password error";
		return;
	}
	$db = new sqlite3('../main.db');
	$statement = $db->query("SELECT count(*) FROM users WHERE username = $new_user");
	while($result = $statement->fetchArray(SQLITE3_ASSOC)){
		if($result["count(*)"] == 0){
			$insert = $db->exec("INSERT INTO users('username', 'password') VALUES($new_user, $new_password)");
			echo $_POST["add_user_username"] ." does not exist: ". $result["count(*)"];
		}else{
			echo $_POST["add_user_username"] ." user already exists: ". $result["count(*)"];
		}
	}
	$db->close();
}

if(isset($_POST["new_link"]) && isset($_POST["link_url"])){
    $dbh = new sqlite3('../main.db');
    $table = 'external_links'
    $statement = $db->prepare('INSERT INTO :table(username, title, link) VALUES(:username, :title, :url)');
    $statement->bindparam(':table', $table);
    $statement->bindparam(':username', $_SESSION['username']);
    $statement->bindparam(':title', $_POST['link_title']);
    $statement->bindparam(':url', $_POST['link_url']);
    $result->$statement->execute();
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
<?php
if($_SESSION['user_id'] == 1){
	echo '<h1>Admin controls</h1>';
    echo '<h2>Add user</h2>
    <form name="add_user" action="settings.php" method="post">
    <label for="add_user_username">username:</label>
    <input type="text" name="add_user_username"><br />
    <label for="confirm_add_user_username">confirm username:</label>
    <input type="text" name="confirm_add_user_username"><br />
    <label for="add_user_password">password:</label>
    <input type="password" name="add_user_password"><br />
    <label for="confirm_add_user_password">confirm password:</label>
    <input type="password" name="confirm_add_user_password"><br />
    <input type="submit" value="submit">
    </form>';
    echo '<h2>Add link</h2>
    <form name="new_link" action="settings.php" method="post">
    <label for="link_title">link title:</label>
    <input type="text" name="link_title"><br />
    <label for="link_url">link url:</label>
    <input type="text" name="link_url"><br />
    <input type="submit" value="submit">
    </form>';
}
?>
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
