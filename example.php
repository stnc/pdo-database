<?php
require_once 'vendor/autoload.php';
/*use \DB\MYSQL as dbs;
$db = new dbs\Mysql();*/
$db = new \DB\MYSQL\Mysql();
$tableName = 'users';
// multiple rows
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->rows ( $q );
foreach ( $array_expression as $value ) {
	echo  $value ['name'];
	echo '<br>';
}

// single row
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetch ( $q );
foreach ( $array_expression as $value ) {
	echo $value ['name'];
	echo '<br>';
}
die;
// query metod
$q = "ALTER TABLE users MODIFY COLUMN user_id  int(11) NOT NULL AUTO_INCREMENT FIRST";
$this->querys ( $q );

// insert data
$data = array (
		'name' => "john",
		'lastname' => "carter",
		'status' => 1,
		'age' => 25 
);

$this->insert ( $tableName, $data );

// update metod

$data = array (
		'name' => "john",
		'lastname' => "carter",
		'status' => 1,
		'age' => 25 
);

$where = array (
		'user_id' => 1 
);

$this-> update ( $tableName, $data, $where );
// delete data

$where = array (
		'user_id' => 1 
);
return $db->delete ( $tableName, $where );


//last id 
$db->lastID();
