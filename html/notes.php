<?php
$table = "notes";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
if(isset($_POST["action"])){
    $dbh = new sqlite3('../main.db');
    if($_POST["action"] == "new"){
        $prepare = $dbh->prepare('INSERT INTO notes(username, title, note) VALUES(:username, :title, :note)');
        $prepare->bindParam(':username', $_SESSION["username"]);
    }
    if($_POST["action"] == "edit"){
        $prepare = $dbh->prepare('UPDATE notes SET title= :title, note= :note WHERE id = :id');
        $prepare->bindParam(':id', $_POST["id"]);
    }
    if($_POST["action"] == "new" || $_POST["action"] == "edit"){
        $prepare->bindParam(':title', $_POST["title"]);
        $prepare->bindParam(':note', $_POST["note"]);
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
<?php page_navigation($table, $post_count); ?>
</div>
<div class="main">
<div class="content">
<?php
echo "<p  id='newform'><button class='database' onclick=\"javascript:newNote()\">new</button></p>";
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
	$offset = $_GET["offset"];
} else {
	$offset = 0;
}
$prepare = $dbh->prepare("SELECT * FROM notes ORDER BY id DESC LIMIT :limit OFFSET :offset");
$prepare->bindParam(':limit', $post_count);
$prepare->bindParam(':offset', $offset);
$result=$prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
	echo "<div class='post' id='post_" . $row["id"] . "'>
    <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
    <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
    <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>";
	if($_SESSION["username"] == $row["username"]){
		echo "<button class='database' onclick=\"javascript:editNote('".$row["id"]."')\">
        edit
        </button>
        <button class='database' onclick=\"javascript:archive('notes', '".$row["id"]."')\">
        archive
        </button>";
	}
	echo "<div class='clearer'>
    <span>
    </span>
    </div>
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
<?php page_navigation($table, $post_count); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
