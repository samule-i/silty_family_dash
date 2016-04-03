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
$db = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $_GET["offset"];
} else {
	$offset = 0;
}
$result = $db->query("SELECT * FROM rules_archive ORDER BY id DESC LIMIT 5 OFFSET $offset");
while($row = $result->fetchArray(SQLITE3_ASSOC)){
    if($_SESSION["user_id"]==1 || in_array($_SESSION["username"], explode(',',$row['applies_to']))){
        echo "<div class='post' id='post_" . $row["id"] . "'><h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
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
