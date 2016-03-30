<?php
$table = "diary_archive";
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
$prepare = $dbh->prepare("SELECT * FROM diary_archive ORDER BY id DESC LIMIT :limit OFFSET :offset");
$prepare->bindParam(':limit', $post_count);
$prepare->bindParam(':offset', $offset);
$result=$prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	echo "<div class='post' id='post_" . $row["id"] . "'>
    <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
    <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
    <p id='content_" . $row["id"] . "'>" . $row["content"] . "</p>
    <div class='clearer'><span></span></div>
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
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
