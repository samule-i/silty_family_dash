<?php
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
            $t_cost += $row["cost"];
            $return[$sltyusr] = $t_cost;
        }
    }
    return $return;
}

foreach(user_spent() as $y=>$x){
    echo $y.','.$x.'<br>';
}
?>
