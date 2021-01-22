<?php
require_once(__DIR__.'/../login.php');
$tableName = "blackjack";

function connectToMySQL():bool{
    global $servername;
    global $username;
    global $password;
    global $testConn;
    try{
        $testConn = new mysqli($servername,$username,$password);

    }catch(Exception $e){
        return true;
    }

    if($testConn->connect_error){
        return true;
    }else{
        return false;
    }
}

function closeMySQLConnection(){
    global $testConn;

    $testConn->close();
}

function connectToDatabase():bool{
    global $servername;
    global $username;
    global $password;
    global $dataBaseName;
    global $conn;

    connectToMySQL();
    createSQLIDatabase();

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dataBaseName",
            $username,
            $password);

        $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        createTable();
        return false;

    }catch(PDOException $e){
        echo "Connection failed: ". $e->getMessage();
        return true;
    }
}

function createSQLIDatabase(){
    global $testConn;
    global $dataBaseName;
    $sql = "CREATE DATABASE IF NOT EXISTS ".$dataBaseName;

    try{
        $testConn->query($sql);
        return false;
    }catch(exception $e){
        return true;
    }
}

function createTable(){
    global $conn;
    global $tableName;
    $sql = "CREATE TABLE IF NOT EXISTS ".$tableName." (
                name VARCHAR(15),
                username VARCHAR(15),
                balance SMALLINT,
                wins SMALLINT,
                losses SMALLINT)";
    if( $conn->query($sql) == TRUE){
        return false;
    }else{
        return true;
    }
}


function getUsers(){
    global $tableName;
    global $conn;
    $query = "SELECT username FROM $tableName";
    return $conn->query($query)->fetchALL(PDO::FETCH_COLUMN);
}

function getUserData($user){
    global $tableName;
    global $conn;
    $correctedusername = '\''.$user.'\'';
    $query = "SELECT * FROM $tableName WHERE username = $correctedusername LIMIT 1";
    return $conn->query($query)->fetch(PDO::FETCH_NUM);
}

function createUser($newname, $newusername, $newbalance){
    global $tableName;
    global $conn;
    $preparedname = '\''.$newname.'\'';
    $preparedusername = '\''.$newusername.'\'';
    $query = "INSERT INTO $tableName VALUES($preparedname,$preparedusername, $newbalance, 0, 0)";
    try{
        $conn->query($query);
    }catch(Exception $e){
        return false;
    }
    return true;
}

function adjustBalance($myusername, $amount){
    global $conn;
    global $dataBaseName;
    $correctedusername = '\''.$myusername.'\'';

    /** GET CURRENT Balance */
    $query = "SELECT balance FROM $dataBaseName WHERE username = $correctedusername";
    $result = $conn->query($query)->fetch(PDO::FETCH_NUM);

    /** UPDATE THE Wins */
    $currentBalance =$result[0] + $amount;

    /** UPDATE THE DATABASE */
    $query = "UPDATE $dataBaseName SET balance=$currentBalance WHERE username = $correctedusername";
    $conn->query($query);
}

function addWin($myusername){
    global $conn;
    global $dataBaseName;
    $correctedusername = '\''.$myusername.'\'';

    /** GET CURRENT WINS */
    $query = "SELECT wins FROM $dataBaseName WHERE username = $correctedusername";
    $result = $conn->query($query)->fetch(PDO::FETCH_NUM);

    /** UPDATE THE Wins */
    $currentWins =$result[0] + 1;

    /** UPDATE THE DATABASE */
    $query = "UPDATE $dataBaseName SET wins=$currentWins WHERE username = $correctedusername";
    $conn->query($query);
}

function addLoss($myusername){
    global $conn;
    global $dataBaseName;
    $correctedusername = '\''.$myusername.'\'';
    /** GET CURRENT LOSSES */
    $query = "SELECT losses FROM $dataBaseName WHERE username = $correctedusername";
    $result = $conn->query($query)->fetch(PDO::FETCH_NUM);
    /** UPDATE THE Losses */
    $currentLosses =$result[0] + 1;
    /** UPDATE THE DATABASE */
    $query = "UPDATE $dataBaseName SET losses=$currentLosses WHERE username = $correctedusername";
    $conn->query($query);
}



//Functions for testing
/*
function addTestRowToTable($tablename, $name, $username, $balance, $wins, $losses){
    global $conn;
    $preparedname = '\''.$name.'\'';
    $preparedusername = '\''.$username.'\'';

    $query = "INSERT INTO $tablename VALUES($preparedname,$preparedusername, $balance, $wins, $losses)";
    try{
        $result = $conn->query($query);
    }catch(Exception $e){
        return false;
    }
    if (!$result){
        return false;
    }else{
        return true;
    }
}


function setdBServerName($myServerName){
    global $servername;
    $servername = $myServerName;
}

function getdBServerName(){
    global $servername;
    return $servername;
}

function setdBUserName($myUserName){
    global $username;
    $username = $myUserName;
}

function getdBUserName(){
    global $username;
    return $username;
}

function setdBPassword($myPassword){
    global $password;
    $password = $myPassword;
}

function getdBPassword(){
    global $password;
    return $password;
}

function setdBname($mydBName){
    global $dataBaseName;
    $dataBaseName = $mydBName;
}

function getdBname(){
    global $dataBaseName;
    return $dataBaseName;
}

function setTableName($myTableName){
    global $tableName;
    $tableName = $myTableName;
}

function getTableName(){
    global $tableName;
    return $tableName;
}

function doesDatabaseExist($myTestDb){
    global $testConn;
    $dbname = '\''.$myTestDb.'\'';
    $query = "SHOW DATABASES LIKE $dbname";
    $result = $testConn->query($query);
    $rows = $result->num_rows;
    if($rows == 1){
        return true;
    }else{
        return false;
     }
}

function doesTableExist($myTestTable){
    global $conn;
    $tablename = '\''.$myTestTable.'\'';
    $query = "SHOW TABLES LIKE $tablename";
    $result = $conn->query($query);
    $rows = $result->rowCount();
    if($rows == 1){
        return true;
    }else{
        return false;
    }
}

function removeSQLIDatabaseIfExists($databaseName){
    global $testConn;
    $query = "DROP DATABASE IF EXISTS $databaseName";
    $testConn->query($query);
}

function removePDODatabaseIfExists($databaseName){
    global $conn;
    $query = "DROP DATABASE IF EXISTS $databaseName";
    $conn->query($query);
}
function removeTableIfExists($tableName){
    global $conn;
    $query = "DROP TABLE IF EXISTS $tableName";
    $conn->query($query);
}

function doesUsernameExist($username){
    global $conn;
    global $tableName;
    $correctedusername = '\''.$username.'\'';
    $query = "SELECT * FROM $tableName WHERE username = $correctedusername";
    $result = $conn->query($query);
    $rows = $result->rowCount();
    if($rows >= 1){
        return true;
    }else{
        return false;
    }
}

function removeUsername($username){
    global $conn;
    global $tableName;
    $correctedusername = '\''.$username.'\'';
    $query = "DELETE FROM $tableName WHERE username = $correctedusername";
    $result = $conn->query($query);

    $rows = $result->rowCount();
    if($rows >= 1){
        return true;
    }else{
        return false;
    }
}
*/
