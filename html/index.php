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