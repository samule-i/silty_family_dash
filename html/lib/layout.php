<?php
function doctype(){
	echo "<!DOCTYPE html> \n";
}

function head(){
	echo "<head>";
	echo "<title>silty</title>";
	echo "<Meta http-equiv='Content-type' content='Text/HTML; charset=utf-8'>";
	echo "<link type='text/css' rel='stylesheet' href='css/style.css' media='screen'>";
	echo "<script type='text/javascript' src='lib/ironserver.js'></script>";
	echo "</head>";
}

function html_header($table){
	echo "<div class='header'>";
	echo "<div class='title'>";
	echo "<h1>♥ silty ♥ - ". $table ."</h1>";
	echo "</div>";
	echo "</div>";
}
function navigation(){
	echo "<div class='navigation'>";
	$navigation = array(
	"home" => "index.php",
	"rules" => "rules.php",
	"diary" => "diary.php",
	"stars" => "stars.php",
	"rewards" => "rewards.php",
	"notes" => "notes.php"
	);
	foreach($navigation as $title=>$url){
		echo "<a href='". $url ."'>".$title."</a>";
	}
	echo "<div class='clearer'><span></span></div>";
	echo "</div>";
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
	echo "<div class='sidenav'>";
	echo "<h1>Logged in as " . $_SESSION['username'] . "</h1>";
	echo "<ul><li><a href=?logout=TRUE>logout</a></li></ul>";
	echo "<h1>" . (total_stars()-spent_stars()) . "/" . total_stars() . " stars</h1>";
	echo "<h1>Archives</h1>";
	echo "<ul>";
	$archives = array(
		"Rules" => "rules.php?archive=1",
	);
	foreach($archives as $title=>$url){
		echo "<li><a href='". $url ."'>".$title."</a></li>";
	}
	echo "</ul>";
	echo "<h1>Links</h1>";
	external_links();
	echo "<h1>Services</h1>";
	echo "<ul>";
	$services = array(
		"Website - Nginx",
		"Voice - Mumble",
		"Torrents - Deluged",
		"File share - Samba",
	);
	foreach($services as $name){
		echo "<li>" . $name ."</li>";
	}
	echo "</ul>";
	echo "</div>";
}

function footer(){
	echo "<div class='footer'>";
	echo "<span class='left'>&copy; 2015 <a href='http://ironserver.co.uk'>ironserver</a>. Valid <a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a> &amp; <a href='http://validator.w3.org/check?uri=referer'>XHTML</a></span>";
	echo "<span class='right'><a href='http://templates.arcsin.se/'>Website template</a> by <a href='http://arcsin.se/'>Arcsin</a></span>";
	echo "<span class='right'><a href='copyright.php'>copyright information</a></span>";
	echo "<div class='clearer'><span></span></div>";
	echo "</div>";
}
?>