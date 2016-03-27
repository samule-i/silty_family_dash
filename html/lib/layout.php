<?php
function doctype(){
	echo "<!DOCTYPE html> \n";
}

function head(){
	echo "<head>
    <title>silty</title>
    <Meta http-equiv='Content-type' content='Text/HTML; charset=utf-8'>
    <link type='text/css' rel='stylesheet' href='css/style.css' media='screen'>
    <script type='text/javascript' src='lib/ironserver.js'></script>
    </head>";
}

function html_header($table){
	echo "<div class='header'>
    <div class='title'>
    <h1>♥ silty ♥ - ". $table ."</h1>
    </div>
    </div>";
}
function navigation(){
	echo "<div class='navigation'>";
	$navigation = array(
	"home" => "index.php",
	"rules" => "rules.php",
	"diary" => "diary.php",
	"stars" => "stars.php",
	"rewards" => "rewards.php",
	"notes" => "notes.php",
    "gallery" => "gallery.php",
	"settings" => "settings.php"
	);
	foreach($navigation as $title=>$url){
		echo "<a href='". $url ."'>".$title."</a>";
	}
	echo "<div class='clearer'>
    <span>
    </span>
    </div>
    </div>";
}

function external_links(){
	$db_path = '../main.db';
	$db = new sqlite3($db_path);
	$result = $db->query("SELECT * FROM external_links");
	echo "<ul>";
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		echo "<li><a target='_blank' href='" . $row["link"] . "'>" . $row["title"] . "</a></li>";
	}
	echo "</ul>";
	$db->close();
}

function sidenav(){
	echo "<div class='sidenav'>
    <h1>Logged in as " . $_SESSION['username'] . "</h1>
    <ul><li><a href=?logout=TRUE>logout</a></li></ul>
    <h1>" . (total_stars()-spent_stars()) . "/" . total_stars() . " stars</h1>
    <ul>";
    foreach(user_stars() as $usr=>$stars){
        echo "<li>".$usr.": ".$stars."</li>";
    }
    echo "</ul>
    <h1>Archives</h1>
    <ul>";
	$archives = array(
		"Rules" => "rules.php?archive=1",
	);
	foreach($archives as $title=>$url){
		echo "<li><a href='". $url ."'>".$title."</a></li>";
	}
	echo "</ul>
    <h1>Links</h1>";
	external_links();
	echo "<h1>Services</h1>
    </div>";
}

function footer(){
	echo "<div class='footer'>
    <span class='left'>&copy; 2015 <a href='http://ironserver.co.uk'>ironserver</a>. Valid <a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a> &amp; <a href='http://validator.w3.org/check?uri=referer'>XHTML</a></span>
    <span class='right'><a href='copyright.php'>copyright information</a></span>
    <div class='clearer'><span></span></div>
    </div>";
}
?>
