<?php
require_once 'vendor/autoload.php';
  define('DB_TYPE', 'mysql');
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'zf1app_db');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  

/* //use 1 
use stnc\db\MysqlAdapter ;
$db = new MysqlAdapter();
*/

/* //use 2
use stnc\db\MysqlAdapter as dbs;
$db = new dbs();
*/


//use 3
$db = new stnc\db\MysqlAdapter();


$tableName = 'users';
// multiple rows
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetchAll ( $q );
foreach ( $array_expression as $value ) {
	echo  $value ['username'];
	echo '<br>';
}

// single row
$q = "SELECT * FROM ".$tableName ." where id=1";
$array_expression = $db->fetch ( $q );
//print_r($array_expression);

	echo $value ['username'];


// query metod
$q = "ALTER TABLE users MODIFY COLUMN id  int(11) NOT NULL AUTO_INCREMENT FIRST";
 $db->query ( $q );

// insert data
$data = array (
		'first_name' => "john",
		'last_name' => "carter",
		'username' => "rob",
		'password' => "12345",

);

 $db->insert ( $tableName, $data );

// update metod

$data = array (
		'first_name' => "john",
		'last_name' => "carter",
);

$where = array (
		'id' => 1 
);

 $db-> update ( $tableName, $data, $where );
// delete data

$where = array (
		'id' => 5
);
return $db->delete ( $tableName, $where );


//last id 
$db->lastID();
