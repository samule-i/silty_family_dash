<?php
$table = "stars";
$amount = 20;
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
<?php
if($_SESSION["user_id"]==1){
    page_navigation($table, $amount , 'all');
} else {
    page_navigation($table, $amount , $_SESSION["username"]);
}
?>
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

$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
	$offset = $_GET["offset"];
} else {
	$offset = 0;
}
if($_SESSION["user_id"] == 1){
    $prepare = $dbh->prepare("SELECT * FROM stars ORDER BY id DESC LIMIT :amount OFFSET :offset");
} else {
    $prepare = $dbh->prepare("SELECT * FROM stars  WHERE owner = :username ORDER BY id DESC LIMIT :amount OFFSET :offset");
    $prepare->bindParam(':username', $_SESSION["username"]);
}
$prepare->bindParam(':amount', $amount);
$prepare->bindParam(':offset', $offset);
$result = $prepare->execute();
if(!$result){
    echo $dbh->lastErrorMsg();
    exit();
}
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	# create post
	echo $row["owner"]." :".gmdate('Y-m-d', $row["date"])."★: ".$row["note"].'<br>';
}
$dbh->close();
?>
</div>
<?php
sidenav()
?>
<div class="clearer"><span></span></div>
</div>
<div class="navigation">
<?php
if($_SESSION["user_id"]==1){
    page_navigation($table, $amount , 'all');
} else {
    page_navigation($table, $amount , $_SESSION["username"]);
}
?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
