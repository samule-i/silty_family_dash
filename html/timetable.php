<?php
$table = "timetable";
include("lib/layout.php");
include("lib/ironserver.php");
authentication();
?>
<?php
if(isset($_POST["action"])){
    $db = new sqlite3('../main.db');
    if($_POST["action"] == "edit"){
        $prepare = $db->prepare('UPDATE timetable SET time = :time WHERE id = :id');
        $time=prepare_db_string($_POST["time"]);
        $prepare->bindparam(':time', $time);
        $prepare->bindParam(':id', $_POST["id"]);
        $result = $prepare->execute();
        if(!$result){
            echo $db->lastErrorMsg();
            exit();
        }
    }
    $db->close();
    $returnto = "timetable.php#post_".$_POST["id"];
    header("location:".$returnto);
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
</div>
<div class="main">
<div class="content">
<?php
//connect to local database
$db = new sqlite3('../main.db');
//create table if it doesn't exist
$prepare = $db->prepare('CREATE TABLE IF NOT EXISTS timetable(id INTEGER PRIMARY KEY AUTOINCREMENT, date INTEGER NOT NULL, time TEXT DEFAULT "FREE")');
$execute = $prepare->execute();
//for each day following today, find it in the table, if not existing create it.
$countstmnt = $db->prepare('SELECT count(*) FROM timetable WHERE date = :date');
for ($x = 0; $x <=30; $x++){
  //define date for today +x, giving correct dates for next 30 days
  $date = mktime(0, 0, 0, date("m"), date("d")+$x, date(Y));
  $countstmnt->bindparam(':date', $date);
  $countres = $countstmnt->execute();
  while($countrow = $countres->fetchArray(SQLITE3_ASSOC)){
    if($countrow["count(*)"] == 0){
      $insert = $db->prepare('INSERT INTO timetable(date) VALUES(:date)');
      $insert->bindparam(':date', $date);
      $complete=$insert->execute();
      $insert->reset();
    } else {
      $selectstmnt = $db->prepare('SELECT * FROM timetable WHERE date = :date');
      $selectstmnt->bindparam(':date', $date);
      $selectres = $selectstmnt->execute();
      while($selectrow = $selectres->fetchArray(SQLITE3_ASSOC)){
        echo '<div class="post" id="post_' . $selectrow["id"] . '">';
        echo "<div class='controls'><button class='database' onclick=\"javascript:editTimetable('".$selectrow["id"]."')\">edit</div>";
        if($x==0){
          echo '<h1>today</h1>';
        }else{
          echo '<h1>'.gmdate("D-d", $selectrow["date"]).'</h1><i>'.gmdate("M", $selectrow["date"]).'</i>';
        }
        echo '<p id="time_'.$selectrow["id"].'">'.$selectrow["time"].'</p></div>';
      }
  }
  //reset statement within loop next iteration
  $prepare->reset();
  }
}
$db->close();
?>
</div>
<?php
sidenav();
?>
<div class="clearer"><span></span></div>
</div>
<div class="navigation">
</div>
<?php footer(); ?>
</div>
</body>
</html>
