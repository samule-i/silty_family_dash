<?php
#authentication
if(isset($_GET["logout"])){
	session_start();
	session_unset();
	session_destroy();
}
$db_path = '../main.db';

function authentication(){
	session_start();
	if(isset($_SESSION["username"])){ //If no username, go to login page.
		$username = "'" . $_SESSION["username"] . "'";
	} else {
		header("location: login.php");
		exit();
	}
}
function prepare_db_string($str_in){
    $str_in=htmlspecialchars($str_in);
    $str_in=nl2br($str_in);
    return $str_in;
}
function download_file($url, $path){#
    //set getok
    $getok=1;
    $target_dir='/img/rewards/';
    $target_file=basename($url);
    $file = file_get_contents($url);
    $filetype = pathinfo($url,PATHINFO_EXTENSION);
    $check = getimagesize($url);
    if($check !== false) {
        $getok = 1;
    } else {
        $getok = 0;
    }
    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $getok = 0;
    }
    // Allow certain file formats
    if($filetype != "jpg" && $filetype != "png" && $filetype != "jpeg"
    && $filetype != "gif" ) {
        $getok = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if($getok == 0) {
        echo "Sorry, your file was not uploaded.";
    }else{
        $clean_name = preg_replace("/[^A-Z]+/", "", basename($url)).".".$filetype;
    	$result = file_put_contents($_SERVER['DOCUMENT_ROOT'].'/img/rewards/'.$clean_name, $file);
    	return $path . $clean_name;
    }
}

function list_users(){
    $dbh = new sqlite3('../main.db');
    $prepare = $dbh->prepare("SELECT username FROM users WHERE NOT id = '1'");
    $result = $prepare->execute();
    if(!$result){
        echo $dbh->lastErrorMsg();
        exit();
    }
    while($row = $result->fetchArray(SQLITE3_ASSOC)){
        $userlist[] = $row["username"];
    }
    return $userlist;
}
function get_total($table, $user){
	$dbh = new sqlite3('../main.db');
    if($user == 'all'){
        switch($table){
            case "rules":
                $prepare = $dbh->prepare('SELECT count(*) FROM rules');
                break;
            case "diary":
                $prepare = $dbh->prepare('SELECT count(*) FROM diary');
                break;
            case "stars":
                $prepare = $dbh->prepare('SELECT count(*) FROM stars');
                break;
            case "rewards":
                $prepare = $dbh->prepare('SELECT count(*) FROM rewards');
                break;
            case "notes":
                $prepare = $dbh->prepare('SELECT count(*) FROM notes');
                break;
            case "gallery":
                $prepare = $dbh->prepare('SELECT count(*) FROM gallery');
                break;
            case "rules_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM rules_archive');
                break;
            case "diary_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM diary_archive');
                break;
            case "rewards_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM rewards_archive');
                break;
            case "notes_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM notes_archive');
                break;
        }
    } else {
        switch($table){
            case "rules":
                $prepare = $dbh->prepare('SELECT count(*) FROM rules WHERE username = :username');
                break;
            case "diary":
                $prepare = $dbh->prepare('SELECT count(*) FROM diary WHERE username = :username');
                break;
            case "stars":
                $prepare = $dbh->prepare('SELECT count(*) FROM stars WHERE owner = :username');
                break;
            case "rewards":
                $prepare = $dbh->prepare('SELECT count(*) FROM rewards WHERE owner = :username');
                break;
            case "notes":
                $prepare = $dbh->prepare('SELECT count(*) FROM notes WHERE username = :username');
                break;
            case "gallery":
                $prepare = $dbh->prepare('SELECT count(*) FROM gallery WHERE username = :username');
                break;
            case "rules_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM rules_archive WHERE username = :username');
                break;
            case "diary_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM diary_archive WHERE username = :username');
                break;
            case "rewards_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM rewards_archive WHERE username = :username');
                break;
            case "notes_archive":
                $prepare = $dbh->prepare('SELECT count(*) FROM notes_archive WHERE username = :username');
                break;
        }
        $prepare->bindParam(':username', $user);
    }
    $result = $prepare->execute();
	if(!$result){
		echo $dbh->lastErrorMsg();
		echo "count error";
		exit();
	}
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		return $row["count(*)"];
	}
	$dbh->close();
}
function spent_stars(){
	global $db_path;
	$dbh = new sqlite3('../main.db');
    $prepare = $dbh->prepare("SELECT cost FROM rewards where not award_date = ''");
    $result= $prepare->execute();
	if(!$result){
		echo $dbh->lastErrorMsg();
		echo "count error";
		exit();
	}
	while($row = $result->fetchArray(SQLITE3_ASSOC) ){
		$spent += $row["cost"];
	}
    $prepare = $dbh->prepare("SELECT cost FROM rewards_archive where not award_date = ''");
    $result= $prepare->execute();
    if(!$result){
        echo $dbh->lastErrorMsg();
        echo "count error";
        exit();
    }
    while($row = $result->fetchArray(SQLITE3_ASSOC) ){
        $spent += $row["cost"];
    }
	return $spent;
	$dbh->close();
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
function page_navigation($table, $page, $user){
	$page_count = ceil(get_total($table, $user) / $page);
	for($x = 1; $x<=$page_count ;$x++) {
		echo "<a href='?offset=" . ((($x -1) * $page));
		echo "'>" . $x . "</a>";
	}
}
?>
