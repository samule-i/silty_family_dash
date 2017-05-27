<?php
require "lib/password.php";
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
$table="settings";
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
        }else{
            echo "<script type='text/javascript'>alert('username not changed')</script>";
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
		}else{
            echo "<script type='text/javascript'>alert('password not changed')</script>";
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
			$new_prepare->execute();
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

//form for adding an avatar image, and I really don't want to mess around with files again ffs.
/**if(isset($_FILES["image_upload"])){
    $target_dir = $_SERVER['DOCUMENT_ROOT']."/img/avatars/";
    $target_file = $target_dir . basename($_FILES["image_upload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image_upload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
            echo "The file ". $target_file. " has been uploaded.";
            $dbh = new sqlite3('../main.db');
            $prepare = $dbh->prepare("UPDATE users SET avatar=? WHERE username=?");
            $image_path=$target_file;
            $prepare->bindParam(1, $image_path);
            $prepare->bindParam(2, $_SESSION["username"]);
            $result = $prepare->execute();
            if(!$result){
                echo $dbh->lastErrorMsg();
            }
            $dbh->close();
            #header("location: ../settings.php");
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}**/
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
<label for="new_password">New Password:</label>
<input type="password" name="new_password">
<label for="confirm_password">Confirm Password:</label>
<input type="password" name="confirm_password">
<input type="submit" value="submit">
</form>
<!--<h2>Set avatar</h2>
<form  name="image_upload"action="settings.php" method="post" enctype="multipart/form-data">
<label for="image_upload">Upload avatar:</label>
<input type="file" name="image_upload" id="image_upload">
<input type="submit" value="submit">
</form>-->
<?php
if($_SESSION['user_id'] == 1){
	echo '<h1>Admin controls</h1>';
    echo '<h2>Add user</h2>
    <p>Add a user with default password "silty"</p>
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
