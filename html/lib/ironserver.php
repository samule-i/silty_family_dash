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
function list_users(){
    $dbh = new sqlite3('../main.db');
    $prepare = $dbh->prepare("SELECT username FROM users WHERE NOT id = '1'");
    $result = $prepare->execute();
    if(!result){
        echo $dbh->lastErrorMsg();
        exit();
    }
    while($row = $result->fetchArray(SQLITE3_ASSOC)){
        $userlist[] = $row["username"];
    }
    return $userlist;
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

function user_stars(){
    $dbh = new sqlite3('../main.db');
    foreach(list_users() as $sltyusr){
        $prepare = $dbh->prepare('SELECT count(*) FROM stars WHERE owner = :owner');
        $prepare->bindParam(':owner', $sltyusr);
        $result = $prepare->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $starlist[$sltyusr] = $row["count(*)"];
        }
    }
    $dbh->close();
    return $starlist;
}


function user_spent(){
    $dbh = new sqlite3('../main.db');
    foreach(list_users() as $sltyusr){
        $prepare = $dbh->prepare('SELECT cost FROM rewards WHERE owner = :owner AND NOT award_date = ""');
        $prepare->bindParam(':owner', $sltyusr);
        $result = $prepare->execute();
        if(!$result){
            echo $dbh->lastErrorMsg();
        }
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $return[$sltyusr] += $row["cost"];
        }
        $prepare = $dbh->prepare('SELECT cost FROM rewards_archive WHERE owner = :owner AND NOT award_date = ""');
        $prepare->bindParam(':owner', $sltyusr);
        $result = $prepare->execute();
        if(!$result){
            echo $dbh->lastErrorMsg();
        }
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $return[$sltyusr] += $row["cost"];
        }
    }
    return $return;
}


function page_navigation($table, $page){
	$PAGES = ceil(get_total($table) / $page);
	for($x = 1; $x<=$PAGES ;$x++) {
		echo "<a href='?offset=" . ((($x -1) * $page));
		echo "'>" . $x . "</a>";
	}
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
		echo "<div class='post' id='post_" . $row["id"] . "'>
        <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
        <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
        <p id='content_" . $row["id"] . "'>" . $row["content"] . "</p>\n";
		if($_SESSION["username"] == $row["username"]){
			echo "<button class='database' onclick=\"javascript:editpost({title: 'title', content: 'content'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\">
            edit
            <button class='database' onclick=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\">
            delete
            </button>";
		}
		echo "<div class='clearer'><span></span></div>
        </div>";
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
		echo "<div class='post' id='post_" . $row["id"] . "'>
        <h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
        <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
        <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p>";
		if($_SESSION["username"] == $row["username"]){
			echo "<button class='database' onclick=\"javascript:editpost({title: 'title', note: 'note'}, {table: '" . $table ."', username: '" . $_SESSION["username"] . "'}, " . $row["id"] . ")\">
            edit</button>
            <button class='database' onclick=\"javascript:deletePost({table: '" . $table . "', id: '" . $row["id"] . "'})\">
            delete
            </button>";
		}
		echo "<div class='clearer'>
        <span>
        </span>
        </div>
        </div>";
	}
	$db->close();
}
?>
