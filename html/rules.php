<?php
$table = "rules";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
if(isset($_POST["action"])){
    $dbh = new sqlite3('../main.db');
    if($_POST["action"] == "new"){
        $prepare = $dbh->prepare('INSERT INTO rules(username, title, note, applies_to) VALUES(:username, :title, :note, :applies_to)');
        $prepare->bindParam(':username', $_SESSION["username"]);
    }
    if($_POST["action"] == "edit"){
        $prepare = $dbh->prepare('UPDATE rules SET title= :title, note= :note WHERE id = :id');
        $prepare->bindParam(':id', $_POST["id"]);
    }
    if($_POST["action"] == "new" || $_POST["action"] == "edit"){
        $prepare->bindParam(':title', $_POST["title"]);
        $prepare->bindParam(':note', $_POST["note"]);
        $prepare->bindParam(':applies_to', implode(',', $_POST['users']));
        $result = $prepare->execute();
        if(!$result){
            echo $dbh->lastErrorMsg();
            exit();
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
<div class="navigation">
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<div class="main">
<div class="content">
<?php
foreach(list_users() as $s){
    if(!isset($users_str)){
        $users_str = '\''.$s.'\'';
    } else {
        $users_str = $users_str.','.'\''.$s.'\'';
    }
}
if($_SESSION["user_id"]==1){
    echo "<p  id='newform'><button class='database' onclick=\"javascript:newRule($users_str)\">
    new
    </button>\n</p>";
}
$db = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $_GET["offset"];
} else {
	$offset = 0;
}
$result = $db->query("SELECT * FROM rules ORDER BY id DESC LIMIT 5 OFFSET $offset");
while($row = $result->fetchArray(SQLITE3_ASSOC)){
    if($_SESSION["user_id"]==1 || in_array($_SESSION["username"], explode(',',$row['applies_to']))){
        echo "<div class='post' id='post_" . $row["id"] . "'>";
        if($_SESSION["user_id"] == 1){
            echo "<div class='controls'><button class='database' onclick=\"javascript:editReward('".$row["id"]."')\">
            edit
            </button>
            <button class='database' onclick=\"javascript:archive('notes', '".$row["id"]."')\">
            archive
            </button></div>";
        }
        echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
        <h1 id='applies_to_'".$row["id"]."'>".$row["applies_to"]."</h1>
        <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
        <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p><div class='clearer'>
        <span>
        </span>
        </div>
        </div>";
    }
}
$db->close();
?>
</div>
<?php
sidenav()
?>
<div class="clearer"><span></span></div>
</div>
<div class="navigation">
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
