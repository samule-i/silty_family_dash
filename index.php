<?php
$table = "home";
require "lib/password.php";
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
<div class="main">
<div class="content">
<?php
echo "<h1>Welcome " . $_SESSION["username"] . "</h1>";
$dbh=new sqlite3('../main.db');
$prepare=$dbh->prepare('SELECT title, content FROM diary ORDER BY id DESC LIMIT 1');
$result=$prepare->execute();
while($row=$result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class='post'><h2>Most recent diary entry</h2><h3>".$row['title']."</h3><p>".substr($row['content'],0,240)."</p></div>";
}
$prepare=$dbh->prepare('SELECT title, note FROM rules ORDER BY id DESC LIMIT 1');
$result=$prepare->execute();
while($row=$result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class='post'><h2>Most recent rules entry</h2><h3>".$row['title']."</h3><p>".substr($row['note'],0,240)."</p></div>";
}
$prepare=$dbh->prepare('SELECT title, image FROM rewards ORDER BY id DESC LIMIT 1');
$result=$prepare->execute();
while($row=$result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class='post'><h2>Most recent rewards entry</h2><h3>".$row['title']."</h3><p><img class='reward' src='".$row['image']."'></p></div>";
}
$prepare=$dbh->prepare('SELECT title, note FROM notes ORDER BY id DESC LIMIT 1');
$result=$prepare->execute();
while($row=$result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class='post'><h2>Most recent notes entry</h2><h3>".$row['title']."</h3><p>".substr($row['note'],0,240)."</p></div>";
}
$prepare=$dbh->prepare('SELECT image FROM gallery ORDER BY id DESC LIMIT 1');
$result=$prepare->execute();
while($row=$result->fetchArray(SQLITE3_ASSOC)){
    echo "<div class='post'><h2>Most recent gallery upload</h2><p><img class='gallery' src='".$row['image']."'></p></div>'";
}
$dbh->close();
?>

</div>
<?php
sidenav()
?>
<div class="clearer"><span></span></div>
</div>
<?php footer(); ?>
</div>
</body>
</html>
