<?php
$table = "stars";
$post_count = 100;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>

<?php
if(isset($_POST["owner"])){
    $dbh = new sqlite3('../main.db');
    $prepare = $dbh->prepare("INSERT INTO stars(username, note, owner) VALUES(:username, :note, :owner)");
    $prepare->bindParam(':username', htmlspecialchars($_SESSION["username"]));
    $prepare->bindParam(':note', htmlspecialchars($_POST["note"]));
    $prepare->bindParam(':owner', htmlspecialchars($_POST["owner"]));
    $result = $prepare->execute();
    if(!$result){
        echo $dbh->lastErrorMsg();
        exit();
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
<div class="navigation">
<?php page_navigation($table, $post_count); ?>
</div>
<div class="main">
<div class="content">
<?php
#get userlist string
foreach(list_users() as $s){
    if(!isset($users_str)){
        $users_str = '\''.$s.'\'';
    } else {
        $users_str = $users_str.','.'\''.$s.'\'';
    }
}
if($_SESSION["user_id"] == '1'){
	echo "<p  id='newstar'>
    <button class='database' onclick=\"javascript:newStar($users_str)\">
    new
    </button>\n
    </p>";
}
stars($table, $post_count);
?>
</div>
<?php
sidenav()
?>
<div class="clearer"><span></span></div>
</div>
<div class="navigation">
<?php page_navigation($table, $post_count); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
