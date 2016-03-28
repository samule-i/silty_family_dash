<?php
$table = "rewards";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>

<?php
if(isset($_POST["submit"])){
    $dbh = new sqlite3('../main.db');
    if($_POST["action"] == "new"){
        $prepare = $dbh->prepare('INSERT INTO rewards(username, title, note, cost, image, link, owner) VALUES(:username, :title, :note, :cost, :image, :link, :owner)');
        $prepare->bindParam(':username', $_SESSION["username"]);
        $prepare->bindParam(':owner', $_POST["owner"]);
    }
    if($_POST["action"] == "edit"){
        $prepare = $dbh->prepare('UPDATE rewards SET title= :title, note= :note, cost= :cost, image= :image, link= :link WHERE id = :id');
        $prepare->bindParam(':id', $_POST["id"]);
    }
    if($_POST["action"] == "new" || $_POST["action"] == "edit"){
        $prepare->bindParam(':title', $_POST["title"]);
        $prepare->bindParam(':note', $_POST["note"]);
        $prepare->bindParam(':cost', $_POST["cost"]);
        $prepare->bindParam(':image', $_POST["image"]);
        $prepare->bindParam(':link', $_POST["link"]);
    }
    if($_POST["action"] == "archive"){
        $prepare = $dbh->prepare('INSERT INTO rewards_archive SELECT * FROM rewards WHERE id = :id');
        $prepare = $dbh->bindParam(':id', $_POST["id"]);
    }
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
echo "<p  id='newform'><button class='database' onclick=\"javascript:newReward($users_str)\">
new
</button>\n</p>";
rewards($table, $post_count);
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
