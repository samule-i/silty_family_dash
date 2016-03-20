<?php
$table = "rules";
if(isset($_GET["archive"])){
	$table = "rules_archive";
} else {
	$table = "rules";
}
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
<?php page_navigation($table, $post_count); ?>
</div>
<div class="main">
<div class="content">
<?php
echo "<button class='database' onclick=\"javascript:newform({title: 'title', note: 'note'}, {table: '" . $table . "', username: '" . $_SESSION["username"] . "'})\">
new
</button>\n
<p  id='createPost'></p>";
$db = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $_GET["offset"];
} else {
	$offset = 0;
}
$result = $db->query("SELECT * FROM rules ORDER BY id DESC LIMIT 5 OFFSET $offset");
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	echo "<div class='post' id='post_" . $row["id"] . "'>
    <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
    <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
    <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>";
	if($_SESSION["username"] == $row["username"]){
		echo "<button class='database' onclick=\"javascript:editpost({title: 'title', note: 'note'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\">
        edit</button>
        <button class='database' onclick=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\">
        delete
        </button>";
	}
	echo "<div class='clearer'>
    <span>
    </span>
    </div>
    </div>";
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
<?php page_navigation($table, $post_count); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
