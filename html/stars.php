<?php
$table = "stars";
$post_count = 100;
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
if($_SESSION["user_id"] == '1'){
	echo "<button class='database' href=\"javascript:newform({note: 'note', owner: 'owner'}, {username: '".$_SESSION['username'].", 'table: '" . $table . "'})\">
    new
    </button>\n
    <p  id='createPost'></p>";
}
stars($table, $post_count);
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
