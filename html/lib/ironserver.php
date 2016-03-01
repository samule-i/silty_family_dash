<?php
#authentication
if(isset($_GET["logout"])){
	session_start();
	session_unset();
	session_destroy();
}
$db_path = '../main.db';

function authentication(){
	global $db_path;
	session_start();
	if(isset($_SESSION["username"])){ //If no username, go to login page.
		$username = "'" . $_SESSION["username"] . "'";
	} else {
		header("location: login.php");
		exit();
	}
}

function add_user($username){
	global $db_path;
	$db = new sqlite3($db_path);
	$return = $db->query("SELECT username FROM users");
	if(in_array($username, $return)){
		echo "user already exists";
	} else {
		$username = "'".$username."'";
		$statement = $db->prepare('INSERT INTO users (username) VALUES (:username;)');
		$statement->bindValue(':username', $username);
		$result = $statement->execute();
	}
	$db->close();
}

function get_total($table){
	global $db_path;
	$db = new sqlite3($db_path);
	$ret = $db->query("SELECT count(*) FROM $table");
	if(!ret){
		echo $db->lastErrorMsg();
		echo "count error";
		exit();
	}
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		return $row["count(*)"];
	}
	$db->close();
}

function spent_stars(){	
	global $db_path;
	$db = new sqlite3($db_path);
	$ret = $db->query("SELECT cost FROM rewards where not award_date = ''");
	if(!ret){
		echo $db->lastErrorMsg();
		echo "count error";
		exit();
	}
	$spent = 0;
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		$spent = $spent + $row["cost"];
	}
	return $spent;
	$db->close();
}

function total_stars(){
	global $db_path;
	$db = new sqlite3($db_path);
	$ret = $db->query("SELECT count(*) FROM stars");
	if(!ret){
		echo $db->lastErrorMsg();
		echo "count error";
		exit();
	}
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		return $row["count(*)"];
	}
	$db->close();
}

function page_navigation($table, $page){
	$PAGES = ceil(get_total($table) / $page);
	for($x = 1; $x<=$PAGES ;$x++) {
		echo "<a href='?offset=" . ((($x -1) * $page));
		if(isset($_GET["archive"])){
			echo "&archive=1";
		}
		echo "'>" . $x . "</a>";
	}
}

function stars($table, $amount){
	global $db_path;
	$db = new sqlite3($db_path);
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	} else {
		$offset = 0;
	}
	$result = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $amount OFFSET $offset");
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		# create post
		if(($row["id"] % 10) == "0" ){
			echo "<br>";
		}
		echo "<img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0218-star-full.svg' title='" . $row["note"] ."'> ";
	}
	$db->close();
}

function rewards($table, $amount){
	global $db_path;
	$db = new sqlite3($db_path);
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	} else {
		$offset = 0;
	}
	$result = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $amount OFFSET $offset");
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		echo "<div class='post' id='post_" . $row["id"] . "'>";
		echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>\n";
		echo "<h1 id='cost_" . $row["id"] . "'>" . $row["cost"]  . "</h1>\n";
		if($row["award_date"]){
			echo "<img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0218-star-full.svg' title='awarded'>";
		} else {
			echo "<img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0216-star-empty.svg' title='not awarded'>";
		}
		echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>\n";
		echo "<img class='reward' src=" . $row["image"] . ">";
		echo "<p id='content_" . $row["id"] . "'>" . $row["note"] . "</p>\n";
		echo "<p class='hidden' id='image_" . $row["id"] . "'>" . $row["image"] . "</p>";
		if($_SESSION["user_id"] == 1){
			if(!$row["award_date"]){
				echo "<a class=right href=\"javascript:editpost({title: 'title', note: 'content'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "', award_date: '" . strftime('%s') . "'}, " . $row["id"] . ")\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0160-gift.svg'</a>";
			}
			echo "<a class=right href=\"javascript:editpost({title: 'title', note: 'content', cost: 'cost', image: 'image'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0008-quill.svg'</a>";
			echo "<a class=right href=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0270-cancel-circle.svg'></a>";
		}
		echo "<div class='clearer'><span></span></div>";
		echo "</div>";
	}
	$db->close();
}

function get_posts($table, $amount){
	global $db_path;
	$db = new sqlite3($db_path);
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	} else {
		$offset = 0;
	}
	$result = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $amount OFFSET $offset");
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		echo "<div class='post' id='post_" . $row["id"] . "'>";
		echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>\n";
		echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>\n";
		echo "<p id='content_" . $row["id"] . "'>" . $row["content"] . "</p>\n";
		if($_SESSION["username"] == $row["username"]){
			echo "<a class=right href=\"javascript:editpost({title: 'title', content: 'content'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0008-quill.svg'</a>";
			echo "<a class=right href=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0270-cancel-circle.svg'></a>";
		}
		echo "<div class='clearer'><span></span></div>";
		echo "</div>";
	}
	$db->close();
}

function notes($table, $amount){
	global $db_path;
	$db = new sqlite3($db_path);
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	} else {
		$offset = 0;
	}
	$result = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $amount OFFSET $offset");
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		echo "<div class='post' id='post_" . $row["id"] . "'>";
		echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>\n";
		echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>\n";
		echo "<p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>\n";
		if($_SESSION["username"] == $row["username"]){
			echo "<a class=right href=\"javascript:editpost({title: 'title', note: 'note'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0008-quill.svg'</a>";
			echo "<a class=right href=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0270-cancel-circle.svg'></a>";
		}
		echo "<div class='clearer'><span></span></div>";
		echo "</div>";
	}
	$db->close();
}


function rules($table, $amount){
	global $db_path;
	$db = new sqlite3($db_path);
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	} else {
		$offset = 0;
	}
	if(isset($_GET["archive"])){
		$archive = 1;
	} else {
		$archive = 0;
	}
	$result = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $amount OFFSET $offset");
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		echo "<div class='post' id='post_" . $row["id"] . "'>";
		echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>\n";
		echo "<div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>\n";
		echo "<p id='content_" . $row["id"] . "'>" . $row["note"] . "</p>\n";
		if($_SESSION["username"] == $row["username"]){
			echo "<a class=right href=\"javascript:editpost({title: 'title', note: 'content'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0008-quill.svg'</a>";
			echo "<a class=right href=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\"><img class='icon' src='img/icons/IcoMoon-Free-master/SVG/0270-cancel-circle.svg'></a>";
		}
		echo "<div class='clearer'><span></span></div>";
		echo "</div>";
	}
	$db->close();
}
?>