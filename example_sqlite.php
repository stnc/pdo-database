<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

/* //use 1 
use Stnc\Db\SQLiteAdapter ;
$db = new SQLiteAdapter();
*/

/* //use 2
use Stnc\Db\SQLiteAdapter as dbs;
$db = new dbs();
*/


//use 3
$db = new Stnc\Db\SQLiteAdapter("sqllite_example.db");


$tableName = 'CRM_IDENTITYLIST';
// multiple rows
echo $q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetchAll ( $q );
foreach ( $array_expression as $value ) {
	echo  $value ['Scope'];
	echo '<br>';
}

// single row
$q = "SELECT * FROM ".$tableName ." where id=1";
$array_expression = $db->fetch ( $q );


	echo $value ['Scope'];



//print_r($array_expression);


/*
// query metod mysq only 
$q = "ALTER TABLE users MODIFY COLUMN id  int(11) NOT NULL AUTO_INCREMENT FIRST";
 $db->query ( $q );
*/

// insert data
$data = array (
		'ID' => 44555,
		'LastNumber' => 44555,
		'Scope' => "carter",
		'Subsidiary' => "carter",
);
//print_r($data );
 $db->insert ( $tableName, $data );

// update metod

$data = array (
    'LastNumber' => 656656,
    'Scope' => "carter2",
    'Subsidiary' => "carter4",
);
$where = array (
		'ID' => 1
);

 $db-> update ( $tableName, $data, $where );

$where = array (
		'ID' => 5
);
$db->delete ( $tableName, $where );


//last id 
$db->lastID();
/*
///NEW MTEOD //Chaining methods where update //orm step 2
$db->where('ID', '=', 1)->update2(['LastNumber' =>656656]);
//orm step 1
$db->tableName=$tableName;
$array_expression = $db->where_test ( 'ID','=','1' );
*/