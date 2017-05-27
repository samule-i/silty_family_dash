<?php
$table = "rewards_archive";
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
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
	$offset = $_GET["offset"];
} else {
	$offset = 0;
}
$prepare = $dbh->prepare("SELECT * FROM rewards_archive ORDER BY id DESC LIMIT :limit OFFSET :offset");
$prepare->bindParam(':limit', $post_count);
$prepare->bindParam(':offset', $offset);
$result = $prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	echo "<div class='post' id='post_" . $row["id"] . "'>
    <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
    <h5 id='cost_" . $row["id"] . "'>" . $row["cost"]  . "★</h5>";
	if($row["award_date"]){
		echo "awarded";
	} else {
		echo "not_awarded";
	}
	echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
    <img id='image_". $row["id"] ."' class='reward' src=" . $row["image"] . ">
    <p><a id='link_".$row["id"]."' class='database' href ='" . $row["link"] . "'>buy</a></p>
    <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>
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
