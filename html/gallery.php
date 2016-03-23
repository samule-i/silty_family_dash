<?php
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
html_header('notes');
navigation();
?>
<div class="navigation">
<?php page_navigation('gallery', 5); ?>
</div>
<div class="main">
<div class="content">
<?php
echo'<div class="silty_create">
<form action="lib/image_upload.php" method="post" enctype="multipart/form-data">
    <h2>Image upload</h2>
    <input type="file" name="image_upload" id="image_upload">
    <input type="hidden" name="username" value="'.$_SESSION["username"].'">
    <input type="submit" value="upload" name="submit">
</form>
</div>';
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $GET["offset"];
} else {
    $offset = 0;
}
$prepare = $dbh->prepare("SELECT * FROM gallery ORDER BY id DESC LIMIT 5 OFFSET :offset");
$prepare->bindParam(':offset', $offset);
$result = $prepare->execute();
if(!result){
    echo $dbh->lastErrorMsg();
    exit();
}
while($row = $result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class=post id='post_" . $row["id"] . "'>
    <img class='gallery' src='" . $row["image"] . "'>
    <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
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
<?php page_navigation('gallery', 5); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
