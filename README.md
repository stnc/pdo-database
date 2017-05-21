# pdo-database
easy ,basic ,simple ORM <br>
A super simple function that returns the full SQL query from your PDO statements<br>
a PDO database service provider for mysql


## 1. Installing-Connections 
```php
require_once 'vendor/autoload.php';
  define('DB_TYPE', 'mysql');
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'alem');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  ```
## 2. Connections
```php
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
```
## 3. Select multiple rows
```php
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetchAll ( $q );
foreach ( $array_expression as $value ) {
	echo  $value ['name'];
	echo '<br>';
}
```
## 3. Select single row
```php
$tableName = 'wp_options';//wordpress 
$q = "SELECT * FROM ".$tableName;
$array_expression = $db->fetch ( $q );
echo $array_expression ['name'];
//or 
$q = 'SELECT * FROM '.$tableName.' where option_name="siteurl" ';
$array_expression = $db->fetch ( $q );
print_r ($array_expression);
echo $array_expression ['option_name'];


```
## 4.  Query 
```php
$q = "ALTER TABLE users MODIFY COLUMN user_id  int(11) NOT NULL AUTO_INCREMENT FIRST";
$this->query ( $q );
```
## 5. insert data
```php
$data = array (
		'name' => "john",
		'lastname' => "carter",
		'status' => 1,
		'age' => 25 
);


$this->insert ( $tableName, $data );
```
## 6. update metod
```php
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
```
## 7. Delete metod
```php
$where = array (
		'user_id' => 1 
);
```

return $db->delete ( $tableName, $where );

## 8. last id 
```php
$db->lastID();
```
