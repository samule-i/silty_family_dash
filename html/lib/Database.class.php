<?php
    class Database{
        function __construct(self){
            $dbh = new sqlite3($_SERVER['DOCUMENT_ROOT']).'/../main.db';
        }
        function list_users(self){
            $user = array()
            $statement = $dbh->prepare('select username from users');
            $result = $statement->execute();
            while($value = $result->fetchArray()){
                array_push($user, $value['username']);
            }
            return $user;
        }
        function __destroy(self){
            dbh->close();
        }
    }
?>
