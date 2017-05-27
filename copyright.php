<?php
$table = "copyright";
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
<h1>Copyright</h1>
<p>Various resources and credits</p>
<h1>NGINX</h1>
<h1>PHP</h1>
<h1>CSS3</h1>

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
