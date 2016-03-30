<?php
$table = "rewards";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>

<?php
if(isset($_POST["action"])){
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
        $result = $prepare->execute();
        if(!$result){
            echo $dbh->lastErrorMsg();
            exit();
        }
    }
    if($_POST["action"] == "award"){
        $prepare = $dbh->prepare('UPDATE rewards SET award_date = :date_now WHERE id = :id');
        $prepare->bindParam(':date_now', strftime('%s'));
        $prepare->bindParam(':id', $_POST["id"]);
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
    page_navigation($table, $post_count , 'all');
} else {
    page_navigation($table, $post_count , $_SESSION["username"]);
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
echo "<p  id='newform'><button class='database' onclick=\"javascript:newReward($users_str)\">
new
</button>\n</p>";
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
	$offset = $_GET["offset"];
} else {
	$offset = 0;
}
$prepare = $dbh->prepare("SELECT * FROM rewards ORDER BY id DESC LIMIT :limit OFFSET :offset");
$prepare->bindParam(':limit', $post_count);
$prepare->bindParam(':offset', $offset);
$result = $prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	echo "<div class='post' id='post_" . $row["id"] . "'>
    <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
    <h1 id='cost_" . $row["id"] . "'>" . $row["cost"]  . "</h1>";
	if($row["award_date"]){
		echo "awarded";
	} else {
		echo "not_awarded";
	}
	echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
    <img id='image_". $row["id"] ."' class='reward' src=" . $row["image"] . ">
    <p><a id='link_".$row["id"]."' class='database' href ='" . $row["link"] . "'>buy</a></p>
    <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>";
	if($_SESSION["username"] == $row["owner"] || $_SESSION["user_id"] == 1){
		if(!$row["award_date"] && $_SESSION["user_id"] == 1){
			echo "<button class='database' onclick=\"javascript:awardReward('".$row["id"]."')\">
            award
            </button>";
		}
		echo "<button class='database' onclick=\"javascript:editReward(".$row["id"].")\">
        edit
        </button>
		<button class='database' onclick=\"javascript:archive('rewards', '".$row["id"]."')\">
        archive
        </button>";
	}
	echo "<div class='clearer'><span></span></div>
    </div>";
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
    page_navigation($table, $post_count , 'all');
} else {
    page_navigation($table, $post_count , $_SESSION["username"]);
}
?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
