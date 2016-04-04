<?php
$table = "rules_archive";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
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
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $_GET["offset"];
} else {
	$offset = 0;
}
$prepare= $dbh->prepare("SELECT * FROM rules_archive ORDER BY id DESC LIMIT 5 OFFSET :offset");
$prepare->bindParam(":offset", $offset);
$result=$prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
    unset($applies_to);
    foreach(explode(',',$row['applies_to']) as $user_id){
        $username_prepare = $dbh->prepare("SELECT username FROM users WHERE id=:id");
        $username_prepare->bindParam(':id', $user_id);
        $username_result= $username_prepare->execute();
        while($users_row = $username_result->fetchArray(SQLITE3_ASSOC)){
            $applies_to[]=$users_row['username'];
        }
    }
    if($_SESSION["user_id"]==1 || in_array($_SESSION["user_id"], explode(',',$row['applies_to']))){
        echo "<div class='post' id='post_" . $row["id"] . "'>
        <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
        <h1 id='applies_to_".$row["id"]."'>".implode(',',$applies_to)."</h1>
        <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
        <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p><div class='clearer'>
        <span>
        </span>
        </div>
        </div>";
    }
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
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
