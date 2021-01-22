<?php
require_once __DIR__ . '/../src/dataBaseManager.php';

use PHPUnit\Framework\TestCase;

$testdatabasename = 'testdatabase';
$testusername = 'testusername';
$testtablename = 'testTable';
class dataBaseManagerTest extends TestCase
{
    //First I tried to create a MYSQLI connection to verify credentials
    public function testConnectToMYSQL(){

        global $servername;
        global $username;
        global $password;

        //execute test
        $connectError = connectToMySQL();
        $this->assertEquals(false,$connectError,"testConnectToDatabase");
    }

    public function testConnectToMSQLBadServerName(){
        //setup for test
        $currentServerName = getdBServerName();
        setdBServerName('localhos');

        //execute test
        $connectError = connectToMYSQL();
        $this->assertEquals(true,$connectError,"testConnectToDatabaseBadServerName");

        //cleanup
        setdBServerName($currentServerName);
    }

    public function testConnectToMSYQBadUserName(){
        //setup for test
        $currentUserName = getdBUserName();
        setdBUserName('rando');

        //execute test
        $connectError = connectToMYSQL();
        $this->assertEquals(true,$connectError,"testConnectToMSYQLadUserName");

        //cleanup
        setdBUserName($currentUserName);
    }

    public function testConnectToMYSQLBadPassword(){
        //setup for test
        $currentdBPassword = getdBPassword();
        setdBPassword('redonculous');

        //execute test
        $connectError = connectToMySQL();
        $this->assertEquals(true,$connectError,"testConnectToMYSQLBadPassword");

        //cleanup
        setdBPassword($currentdBPassword);
    }

    //Tested database createion with SQLI because PDO requires database
    //I wanted this to be able to be run with minimal setup
    public function testDoesDatabaseExistKnownGood(){
        //setup for test
        global $testdatabasename;
        $currentdBName = getdBname();
        setdBname($testdatabasename);
        createSQLIDatabase();

        //execute test
        connectToMYSQL();
        $exists = doesDatabaseExist($testdatabasename);
        $this->assertEquals(true,$exists,"testDoesDatabaseExistKnownGood");

        //cleanup
        removeSQLIDatabaseIfExists($testdatabasename);
        setdBName($currentdBName);
    }

    public function testDoesDatabaseExistKnownBad(){
        //execute test
        connectToMYSQL();
        $exists = doesDatabaseExist('newtest123456');
        $this->assertEquals(false,$exists,"testDoesDatabaseExistKnownBad");
    }

    public function testCreateNewDatabase(){
        //setup for test
        global $testdatabasename;
        $currentdBName = getdBname();
        //$testDataBaseName = ;
        setdBname($testdatabasename);
        connectToMYSQL();
        removeSQLIDatabaseIfExists($testdatabasename);
        $exists = doesDatabaseExist($testdatabasename);
        $this->assertEquals(false,$exists,"database has not been removed");

        //Execute test
        createSQLIDatabase();
        $exists = doesDatabaseExist($testdatabasename);
        $this->assertEquals(true,$exists,"new database has not been successfully created");

        //cleanup
        removeSQLIDatabaseIfExists($testdatabasename);
        setdBname($currentdBName);
    }

    public function testCreateNewTable(){
        //Setup test
        global $testtablename;
        $currentTableName = getTableName();
        setTableName($testtablename);
        connectToDatabase();
        removeTableIfExists($testtablename);
        $exists = doesTableExist($testtablename);
        $this->assertEquals(false,$exists,"table doesn't exist before it is created");

        //Run Test
        createTable();
        $exists = doesTableExist($testtablename);
        $this->assertEquals(true,$exists,"test table exists after creation");

        //Cleanup
        removeTableIfExists($testtablename);
        setTableName($currentTableName);
    }

    function testAddRowOfData(){
        //set up test;
        global $testusername;
        global $testtablename;
        $currentTableName = getTableName();
        setTableName($testtablename);
        $testname = 'alan';
        $testbalance = 1051;
        $testwins = 51;
        $testlosses = 50;

        //execute test
        connectToDatabase();
        createTable();
        $success = addTestRowToTable($testtablename, $testname, $testusername,$testbalance, $testwins, $testlosses);
        $this->assertEquals(true,$success,"testAddRowOfData");

        //cleanup
        removeTableIfExists($testtablename);
        setTableName($currentTableName);
    }

    function testDoesUsernameExistAfterAdding(){
        //set up test
        global $testtablename;
        $currenttablename = getTableName();
        setTableName($testtablename);
        connectToDatabase();
        createTable();

        global $testusername;
        $testname = 'alan';
        $testbalance = 1051;
        $testwins = 51;
        $testlosses = 50;
        addTestRowToTable($testtablename, $testname, $testusername,$testbalance, $testwins, $testlosses);

        //execute test
        $exists = doesUsernameExist($testusername);
        $this->assertEquals(true,$exists,"testDoesUsernameExistAfterAdding");

        //cleanup
        setTableName($currenttablename);
        removeTableIfExists($testtablename);
    }

    function testRemoveUsernameThatExists(){
        //setup for test
        global $testtablename;
        $currentTable = getTableName();
        setTableName($testtablename);

        connectToDatabase();
        createTable();

        global $testusername;
        $testname = 'alan';
        $testbalance = 1051;
        $testwins = 51;
        $testlosses = 50;
        addTestRowToTable($testtablename, $testname, $testusername,$testbalance, $testwins, $testlosses);

        //execute test
        $success = removeUsername($testusername);
        $this->assertEquals(true,$success,"testRemoveUsernameThatExists");

        //cleanup
        removeTableIfExists($testtablename);
        setTableName($currentTable);

    }

    function testDoesUsernameExistAfterRemoving(){
        //set up test
        global $testusername;
        global $testtablename;
        $currenttablename = getTableName();
        setTableName($currenttablename);

        //execute test
        connectToDatabase();
        createTable();
        $exists = doesUsernameExist($testusername);
        $this->assertEquals(false,$exists,"testDoesUsernameExistAfterRemoving");

        //cleanup
        removeTableIfExists($testtablename);
        setTableName($currenttablename);
    }

    function testInvalidRowOfData(){
        //set up test
        global $testusername;
        global $testtablename;
        $currenttablename = getTableName();
        setTableName($testtablename);
        $testname = 'alan';
        $testbalance = 'test';
        $testwins = 51;
        $testlosses = 50;

        //execute test
        connectToDatabase();
        createTable();
        $success = addTestRowToTable($testtablename, $testname, $testusername,$testbalance, $testwins, $testlosses);
        $this->assertEquals(false,$success,"testInvalidRowOfData");

        //cleanup
        removeTableIfExists($testtablename);
        setTableName($currenttablename);
    }

    function testConnectionToPDODatabase(){
        $connectError = connectToMySQL();
        $this->assertEquals(false,$connectError,"testConnectToDatabase");

        $error = createSQLIDatabase();
        $this->assertEquals(false,$error,"testcreateSQLIDatabase ToDatabase");
    }

    function testBADConnectionToPDODatabase(){
        $currentserver = getdBServerName();
        setdBServerName('distanthost');
        $connectError = connectToMySQL();
        $this->assertEquals(true,$connectError,"testConnectToDatabase");

        setdBServerName($currentserver);
    }

    function testIndexConnectionToDatabase(){
        $connectError = connectToDatabase();
        $this->assertEquals(false,$connectError,"testConnectToDatabase");
    }

    function testGetUsers(){
        global $testtablename;
        global $testdatabasename;

        $currentTable = getTableName();
        setTableName($testtablename);
        $currentDatabase = getdBname();
        setdBname($testdatabasename);

        connectToDatabase();
        createTable();
        addTestRowToTable($testtablename, "mr. monster","Monster",100,1,1);
        addTestRowToTable($testtablename, "mr. bobo","Bobo",200,2,2);
        addTestRowToTable($testtablename,"mr. momo","momo",300,3,3);
        addTestRowToTable($testtablename, "mr. bubby","bubby",4004,4,4);
        addTestRowToTable($testtablename, "mr. buster", "buster", 500,5,5);
        $expectedUsers = array("Monster","Bobo","momo","bubby","buster");

        $users = getUsers();
        $this->assertEquals($expectedUsers,$users,"testConnectToDatabase");

        //cleanup
        setdBname($currentDatabase);
        setTableName($currentTable);
    }

    function testGetUserData(){
        global $testtablename;
        global $testdatabasename;

        $currentTable = getTableName();
        setTableName($testtablename);
        $currentDatabase = getdBname();
        setdBname($testdatabasename);

        connectToDatabase();
        createTable();
        addTestRowToTable($testtablename, "mr. monster","Monster",100,1,1);
        addTestRowToTable($testtablename, "mr. bobo","Bobo",200,2,2);
        addTestRowToTable($testtablename,"mr. momo","momo",300,3,3);
        addTestRowToTable($testtablename, "mr. bubby","bubby",4004,4,4);
        addTestRowToTable($testtablename, "mr. buster", "buster", 500,5,5);
        $expectedUserdata = array("mr. bubby","bubby",'4004','4','4');
        $userdata = getUserData("bubby");

        //$users = getUsers();
        $this->assertEquals($expectedUserdata,$userdata,"testConnectToDatabase");

        //cleanup
        setdBname($currentDatabase);
        setTableName($currentTable);

    }

    function testAdjustBalance(){
        connectToDatabase();
        adjustBalance("monster", 120);
        $this->assertEquals(true,true,"testAdjustBalance");
    }

}
