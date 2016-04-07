<?php
if(!isset($_POST['action'])){
	header("location: index.php");
	exit();
}
if($_POST["action"] == "archive"){
    $archive = $_POST["table"]."_archive";
    $dbh = new sqlite3('../../main.db');
    switch($_POST["table"]){
        case "rules":
            $prepare = $dbh->prepare('INSERT INTO rules_archive SELECT * FROM rules WHERE id = :id');
            break;
        case "diary":
            $prepare = $dbh->prepare('INSERT INTO diary_archive SELECT * FROM diary WHERE id = :id');
            break;
        case "rewards":
            $prepare = $dbh->prepare('INSERT INTO rewards_archive SELECT * FROM rewards WHERE id = :id');
            break;
        case "notes":
            $prepare = $dbh->prepare('INSERT INTO notes_archive SELECT * FROM notes WHERE id = :id');
            break;
    }
    $prepare->bindParam(':id', $_POST["id"]);
    $result = $prepare->execute();
    if(!$result){
        echo $dbh->lastErrorMsg();
        exit();
    }
    switch($_POST["table"]){
        case "rules":
            $prepare = $dbh->prepare('DELETE FROM rules WHERE id = :id');
            break;
        case "diary":
            $prepare = $dbh->prepare('DELETE FROM diary WHERE id = :id');
            break;
        case "rewards":
            $prepare = $dbh->prepare('DELETE FROM rewards WHERE id = :id');
            break;
        case "notes":
            $prepare = $dbh->prepare('DELETE FROM notes WHERE id = :id');
            break;
    }
    $prepare->bindParam(':id', $_POST["id"]);
    $result = $prepare->execute();
    if(!$result){
        echo $dbh->lastErrorMsg();
        exit();
    }
    $dbh->close();
    header("location: ../" . $_POST["table"] . ".php");
    exit();
}
?>
