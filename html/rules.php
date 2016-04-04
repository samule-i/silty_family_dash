<?php
$table = "rules";
$post_count = 5;
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
if(isset($_POST["action"])){
    $dbh = new sqlite3('../main.db');
    if($_POST["action"] == "new"){
        $prepare = $dbh->prepare('INSERT INTO rules(username, title, note, applies_to) VALUES(:username, :title, :note, :applies_to)');
        $prepare->bindParam(':username', $_SESSION["username"]);
    }
    if($_POST["action"] == "edit"){
        $prepare = $dbh->prepare('UPDATE rules SET title= :title, note= :note, applies_to= :applies_to WHERE id = :id');
        $prepare->bindParam(':id', $_POST["id"]);
    }
    if($_POST["action"] == "new" || $_POST["action"] == "edit"){
        $prepare->bindParam(':title', $_POST["title"]);
        $prepare->bindParam(':note', $_POST["note"]);
        foreach($_POST['users'] as $username){
            $id_prepare = $dbh->prepare("SELECT id FROM users WHERE username = :username");
            $id_prepare->bindParam(":username", $username);
            $id_result = $id_prepare->execute();
            while($row=$id_result->fetchArray(SQLITE3_ASSOC)){
                $id_array[] = $row["id"];
            }
        }
        $prepare->bindParam(':applies_to', implode(',', $id_array));
        $result = $prepare->execute();
        if(!$result){
            echo $dbh->lastErrorMsg();
            exit();
        }
    }
    $dbh->close();
}
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
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<div class="main">
<div class="content">
<?php
foreach(list_users() as $s){
    if(!isset($users_str)){
        $users_str = '\''.$s.'\'';
    } else {
        $users_str = $users_str.','.'\''.$s.'\'';
    }
}
if($_SESSION["user_id"]==1){
    echo "<p  id='newform'><button class='database' onclick=\"javascript:newRule($users_str)\">
    new
    </button>\n</p>";
}
$dbh = new sqlite3('../main.db');
if(isset($_GET["offset"])){
    $offset = $_GET["offset"];
} else {
	$offset = 0;
}
$prepare= $dbh->prepare("SELECT * FROM rules ORDER BY id DESC LIMIT 5 OFFSET :offset");
$prepare->bindParam(":offset", $offset);
$result=$prepare->execute();
while($row = $result->fetchArray(SQLITE3_ASSOC)){
    unset($applies_to);
    foreach(explode(',',$row['applies_to']) as $user_id){
        $username_prepare = $dbh->prepare("SELECT username FROM users WHERE id=:id");
        $username_prepare->bindParam(':id', $user_id);
        $username_result= $username_prepare->execute();
        while($users_row = $username_result->fetchArray(SQLITE3_ASSOC)){
            $applies_to[]=$users_row['username'];
        }
    }
    if($_SESSION["user_id"]==1 || in_array($_SESSION["user_id"], explode(',',$row['applies_to']))){
        echo "<div class='post' id='post_" . $row["id"] . "'>";
        unset($user_checkbox);
        if($_SESSION["user_id"] == 1){
            foreach(list_users() as $s){
                if(in_array($s, $applies_to)){
                    $user_is_checked = $s.':\'true\'';
                } else {
                    $user_is_checked = $s.':\'false\'';
                }
                if(!isset($user_checkbox)){
                    $user_checkbox = $user_is_checked;
                } else {
                    $user_checkbox = $user_checkbox.','.$user_is_checked;
                }
            }
            echo "<div class='controls'><button class='database' onclick=\"javascript:editRule('".$row["id"]."', {".$user_checkbox."})\">
            edit
            </button>
            <button class='database' onclick=\"javascript:archive('rules', '".$row["id"]."')\">
            archive
            </button></div>";
        }
        echo "<h1 id='title_" . $row["id"] . "'>" . $row["title"] . "</h1>
        <h1 id='applies_to_".$row["id"]."'>".implode(',',$applies_to)."</h1>
        <div class='descr'>" . $row["username"] . ", " . gmdate('Y-m-d', $row['date']) . "</div>
        <p id='note_" . $row["id"] . "'>" . $row["note"] . "</p><div class='clearer'>
        <span>
        </span>
        </div>
        </div>";
    }
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
<?php page_navigation($table, $post_count, 'all'); ?>
</div>
<?php footer(); ?>
</div>
</body>
</html>
