<?php
require "lib/password.php";
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
//POST FORM FOR CHANGE USERNAME
if(isset($_POST["new_username"])){
    $dbh= new sqlite3('../main.db');
    $prepare=  $dbh->prepare("SELECT password FROM users WHERE username = :username");
    $prepare->bindParam(':username', $_SESSION["username"]);
    $query=$prepare->execute();
    while($row = $query->fetchArray(SQLITE3_ASSOC)){
        if(password_verify($_POST['password'], $row['password']) && ($_POST['new_username'] == $_POST['confirm_username']) && !($_POST['new_username'] == '')){
            $prepare = $dbh->prepare("UPDATE users SET username = :new_username WHERE username = :old_username");
            $prepare->bindParam(':new_username', $_POST["new_username"]);
            $prepare->bindParam(':old_username', $_SESSION["username"]);
            $result=$prepare->execute();
        }
    }
    $dbh->close();
}
//POST FORM FOR CHANGE PASSWORD
if(isset($_POST["current_password"])){
	$dbh = new sqlite3('../main.db');
	$prepare = $dbh->prepare("SELECT password FROM users WHERE username = :username");
    $prepare->bindParam(':username', $_SESSION["username"]);
    $result=$prepare->execute();
	if(!$result){
		echo $dbh->lastErrorMsg();
		exit();
	}
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		if(password_verify($_POST['current_password'], $row['password']) && ($_POST['new_password'] == $_POST['confirm_password']) && !empty($_POST['new_password'])){
			$update_prepare= $dbh->prepare("UPDATE users SET password = :password WHERE username = :username");
            $update_prepare->bindParam(':password', password_hash($_POST['new_password'], PASSWORD_DEFAULT));
            $update_prepare->bindParam(':username', $_SESSION['username']);
            $update_result=$update_prepare->execute();
            if(!$update_result){
                echo $dbh->lastErrorMsg();
                exit();
            }
		}
	}
	$dbh->close();
}
//POST FORM FOR ADD USER
if(isset($_POST["add_user_username"])){
	if($_POST["add_user_username"] == !$_POST["confirm_add_user_username"] && !empty($_POST["add_user_username"])){
		echo "username error";
		exit();
	}
	$dbh = new sqlite3('../main.db');
	$prepare = $dbh->prepare("SELECT count(*) FROM users WHERE username = :username");
    $prepare->bindParam(':username', $_POST["username"]);
    $result=$prepare->execute();
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		if($row["count(*)"] == 0){
			$new_prepare= $dbh->prepare("INSERT INTO users('username', 'password') VALUES(:username, :password)");
            $new_prepare->bindParam(':username', $_POST['add_user_username']);
            $new_prepare->bindParam(':password', password_hash("silty", PASSWORD_DEFAULT));
			echo $_POST["add_user_username"] ." does not exist: ". $row["count(*)"];
		}else{
			echo $_POST["add_user_username"] ." user already exists: ". $row["count(*)"];
		}
	}
	$dbh->close();
}
//POST FORM FOR ADD LINK
if(isset($_POST["link_title"]) && isset($_POST["link_url"])){
    $dbh = new sqlite3('../main.db');
    $statement = $dbh->prepare('INSERT INTO external_links(username, title, link) VALUES(:username, :title, :url)');
    $statement->bindvalue(':username', $_SESSION['username']);
    $statement->bindvalue(':title', htmlspecialchars($_POST['link_title']));
    $statement->bindvalue(':url', htmlspecialchars($_POST['link_url']));
    $result = $statement->execute();
    $dbh->close();
}
//POST FORM FOR RESET USER PASSWORD
if(isset($_POST["resetuser"])&&isset($_POST["confirmresetuser"])&&$_POST["resetuser"]==$_POST["confirmresetuser"]){
    //check if user exists
    $dbh = new sqlite3('../main.db');
    $search_user = $dbh->prepare('SELECT count(*) FROM users WHERE username=:username');
    $search_user->bindParam(':username', $_POST["resetuser"]);
    $user_result=$search_user->execute();
    while($row=$user_result->fetchArray(SQLITE3_ASSOC)){
        if($row["count(*)"]==0){
            echo "<script type='text/javascript'>alert('user does not exist')</script>";
        } else {
            $update_password = $dbh->prepare('UPDATE users SET password=:password WHERE username=:username');
            $update_password->bindParam(':username', $_POST["resetuser"]);
            $update_password->bindParam(':password', password_hash("silty", PASSWORD_DEFAULT));
            $update_result=$update_password->execute();
            if($update_result){
                echo "<script type='text/javascript'>alert('user password reset successfully')</script>";
            } else {
                echo "<script type='text/javascript'>alert(".$dbh->lastErrorMsg().")</script>";
            }
        }
    }
    $dbh->close();
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
    <p>Add a user with defualt password "silty"</p>
    <form name="add_user" action="settings.php" method="post">
    <label for="add_user_username">username:</label>
    <input type="text" name="add_user_username"><br />
    <label for="confirm_add_user_username">confirm username:</label>
    <input type="text" name="confirm_add_user_username"><br />
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
    echo '<h2>Reset user password</h2>
    <p>password on user will be re-set to default "silty"</p>
    <form name="reset user password action="settings.php" method="post">
    <label for="resetuser">User to reset</label>
    <input type="text" name="resetuser">
    <label for="confirmresetuser">Confirm username</label>
    <input type="text" name="confirmresetuser">
    <input type="submit" value="submit">';

}
?>
</div>
<?php
sidenav();
?>
<div class="clearer"><span></span></div>
</div>
<?php footer(); ?>
</div>
</body>
</html>
