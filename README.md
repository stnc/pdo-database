# pdo-database
easy ,basic ,simple ORM <br>
A super simple function that returns the full SQL query from your PDO statements<br>
a PDO database service provider for mysql


## 1. Installing
require_once 'vendor/autoload.php';
/*use \DB\MYSQL as dbs;
$db = new dbs\Mysql();*/
  define('DB_TYPE', 'mysql');
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'alem');
  define('DB_USER', 'root');
  define('DB_PASS', '');
## 2. Connections
$db = new db\mysql\mysql();
$tableName = 'users';
// multiple rows
## 3. Select
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->rows ( $q );
foreach ( $array_expression as $value ) {
	echo  $value ['name'];
	echo '<br>';
}
## 3. Select single row
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetch ( $q );
foreach ( $array_expression as $value ) {
	echo $value ['name'];
	echo '<br>';
}

## 4. Select query row
$q = "ALTER TABLE users MODIFY COLUMN user_id  int(11) NOT NULL AUTO_INCREMENT FIRST";
$this->querys ( $q );

## 5. insert data

$data = array (
		'name' => "john",
		'lastname' => "carter",
		'status' => 1,
		'age' => 25 
);


$this->insert ( $tableName, $data );

## 6. update metod
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

## 7. Delete metod
return $db->delete ( $tableName, $where );

## 8. last id 
$db->lastID();
