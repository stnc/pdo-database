<?php

namespace Stnc\Db;

/**
 * A super simple function that returns the full SQL query from your PDO statements
 * a PDO database service provider for mysql
 * Copyright (c) 2015
 *
 * @author Selman TUNÇ <selmantunc@gmail.com>
 * @link https://github.com/stnc/pdo-database
 * @version 1.0.0.0
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
use \PDO;
//implements \DBInterface
class MysqlAdapter extends PDO implements DBInterface {
	public static $dbMysql = false;
	
	public  $tableName ;
	private  $where ;
	
	function __construct() {
		if (self::$dbMysql === false) {
			$this->connect ();
		}
	}
	/**
	 * pdo connector
	 */
	private function connect() {
		$dsn = DB_TYPE . ":dbname=" . DB_NAME . ";host=" . DB_HOST;
		try {
			self::$dbMysql = new PDO ( $dsn, DB_USER, DB_PASS, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
			    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
			) );
			
			self::$dbMysql->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'); // new -> stnc
			self::$dbMysql->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			self::$dbMysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			self::$dbMysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		} catch ( PDOException $e ) {
			
			//  log handler
		}
	}
	
	
	    /**
	*	multiple connection 
     * Static method get
     *
     * @param array $group            
     * @return \lib\database
     */
    public static function get($group = false)
    {
        
        // Determining if exists or it's not empty, then use default group defined in config
        $group = ! $group ? array(
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS
        ) : $group;
        
        // Group information
        $type = $group['type'];
        $host = $group['host'];
        $name = $group['name'];
        $user = $group['user'];
        $pass = $group['pass'];
        
        // ID for database based on the group information
        $id = "$type.$host.$name.$user.$pass";
        
        // Checking if the same
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }
        
        try {
            // I've run into problem where
            // SET NAMES "UTF8" not working on some hostings.
            // Specifiying charset in DSN fixes the charset problem perfectly!
            $instance = new Database("$type:host=$host;dbname=$name;charset=UTF8", $user, $pass, array(
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
            ));
            
            $instance->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'); // new -> stnc
            $instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            /*
             * benim eski mvc den
             * self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             * self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
             */
            
            // Setting Database into $instances to avoid duplication
            self::$instances[$id] = $instance;
            
            return $instance;
        } catch (PDOException $e) {
            // in the event of an error record the error to errorlog.html
           // Logger::newMessage($e);
           // logger::customErrorMsg();
        }
    }

	
	/**
	 * method for multiple rows selecting records from a database
	 * birden fazla kolon bilgisini gonderir
	 *
	 * @param string $sql
	 *        	sql query
	 * @param array $array
	 *        	named params
	 * @param object $fetchMode
	 * @return array returns an array of records
	 * @example $q = " SELECT * FROM users";
	 *          $dbMysql->fetchAll($q);
	 */
	public function fetchAll($sql, $array = array(), $fetchMode = 'array') {
		if ($fetchMode == 'array') {
			$fetchMode = PDO::FETCH_ASSOC;
		} else if ($fetchMode == 'object') {
			$fetchMode = PDO::FETCH_OBJ;
		}
		
		$stmt = self::$dbMysql->prepare ( $sql );
		foreach ( $array as $key => $value ) {
			if (is_int ( $value )) {
				$stmt->bindValue ( "$key", $value, PDO::PARAM_INT );
			} else {
				$stmt->bindValue ( "$key", $value );
			}
		}
		
		$stmt->execute ();
		return $stmt->fetchAll ( $fetchMode );
	}
	
	/**
	 * method for single row selecting records from a database
	 * tek bir datayı/kolon bilgisini gönderir
	 *
	 * @param string $sql
	 *        	sql query
	 * @param array $array
	 *        	named params
	 * @param object $fetchMode
	 * @return array returns an array of records
	 * @return array returns an array of records
	 * @example $q = " SELECT * FROM users";
	 *          $dbMysql->fetch($q);
	 *
	 */
	public function fetch($sql, $fetchMode = 'array') {
		if ($fetchMode == 'array') {
			$fetchMode = PDO::FETCH_ASSOC;
		} else if ($fetchMode == 'object') {
			$fetchMode = PDO::FETCH_OBJ;
		}
		
		$stmt = self::$dbMysql->prepare ( $sql );
		
		$stmt->execute ();
		
		return $stmt->fetch ( $fetchMode );
	}
	
	/**
	 * query method
	 *
	 * @param string $sql
	 *        	query name
	 *
	 *        	$q = "SHOW FULL TABLES";
	 *        	$this->query($q)
	 */
	public function query($sql) {
		$stmt = self::$dbMysql->prepare ( $sql );
		return $stmt->execute ();
	}
	
	/**
	 * insert method
	 *
	 * @param string $table
	 *        	table name
	 * @param array $data
	 *        	array of columns and values
	 * @example $data = array(
	 *          'name' => "john",
	 *          'lastname' => "carter",
	 *          'status' => 1,
	 *          'age' =>25 ,
	 *          );
	 *
	 *          $this->insert('users',$data );
	 */
	public function insert($table, $data) {
		ksort ( $data );
		
		$fieldNames = implode ( ',', array_keys ( $data ) );
		$fieldValues = ':' . implode ( ', :', array_keys ( $data ) );
		
		/*
		$sql = $db->prepare("INSERT INTO db_fruit (id, type, colour) VALUES (:id, :name, :color)");
		$sql->execute(array('id' => $newId, 'name' => $name, 'color' => $color));
		http://bit.ly/2sDDt25
		*/
		$stmt = self::$dbMysql->prepare ( "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)" );
		
		foreach ( $data as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		$stmt->execute ();
		
		
	
		/*    $this->beginTransaction();
		
		    $status = $this->exec($statement);
		    if ($status) {
		        $this->commit();
		    } else {
		        $this->rollback();
		    }
		*/
	}
	
	/**
	 * update method
	 * //güncelleme işlemleri yapan kısım
	 *
	 * @param string $table
	 *        	table name
	 * @param array $data
	 *        	array of columns and values
	 * @param array $where
	 *        	array of columns and values
	 * @example $data = array(
	 *          'name' => "john",
	 *          'lastname' => "carter",
	 *          'status' => 1,
	 *          'age' =>25 ,
	 *          );
	 *
	 *
	 *          $where = array(
	 *          'user_id' => 1
	 *          );
	 *
	 *          $this-> update('users', $data, $where);
	 */
	public function update($table, $data, $where) {
		ksort ( $data );
		
		$fieldDetails = NULL;
		foreach ( $data as $key => $value ) {
			$fieldDetails .= "$key = :$key,";
		}
		$fieldDetails = rtrim ( $fieldDetails, ',' );
		
		$whereDetails = NULL;
		$i = 0;
		foreach ( $where as $key => $value ) {
			if ($i == 0) {
				$whereDetails .= "$key = :$key";
			} else {
				$whereDetails .= " AND $key = :$key";
			}
			
			$i ++;
		}
		$whereDetails = ltrim ( $whereDetails, ' AND ' );
		$s = "UPDATE $table SET $fieldDetails WHERE $whereDetails";
		
		$stmt = self::$dbMysql->prepare ( $s );
		
		foreach ( $data as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		foreach ( $where as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		$stmt->execute ();
	}
	
	/**
	 * Delete method
	 * silme işlemleri yapan kısım
	 *
	 * @param string $table
	 *        	table name
	 * @param array $data
	 *        	array of columns and values
	 * @param array $where
	 *        	array of columns and values
	 * @param integer $limit
	 *        	limit number of records
	 *
	 * @example $where = array(
	 *          'user_id' => 1
	 *          );
	 *          return $dbMysql->delete("users", $where);
	 */
	public function delete($table, $where, $limit = 1) {
		ksort ( $where );
		
		$whereDetails = NULL;
		$i = 0;
		foreach ( $where as $key => $value ) {
			if ($i == 0) {
				$whereDetails .= "$key = :$key";
			} else {
				$whereDetails .= " AND $key = :$key";
			}
			
			$i ++;
		}
		$whereDetails = ltrim ( $whereDetails, ' AND ' );
		
		// if limit is a number use a limit on the query
		if (is_numeric ( $limit )) {
			$uselimit = "LIMIT $limit";
		}
		$sql = "DELETE FROM $table WHERE $whereDetails $uselimit";
		$stmt = self::$dbMysql->prepare ( $sql );
		
		foreach ( $where as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		$stmt->execute ();
	}
	
	/**
	 * son eklenenin id numarası
	 */
	function lastID() {
		return self::$dbMysql->lastInsertId ();
	}
	
	/**
	 * last error info
	 * son hatayı verir
	 */
	function error() {
		return $this->error ();
	}
	
	/**
	 * one result
	 * tek sonuc verir
	 *
	 * @param string $sql
	 *        	table name
	 */
	function result($sql) {
		$val = current ( array_values ( $this->fetch ( $sql ) ) );
		return $val;
	}
	
	/**
	 * truncate table
	 *
	 * @param string $table
	 *        	table name
	 */
	public function truncate($table) {
		return self::$dbMysql->exec ( "TRUNCATE TABLE $table" );
	}
	
		
	/**
	*orm logic test step 1
	*/
	 
	public function where_test ($rowName,$ayrac,$value) {
		$tableName=$this->tableName;
		$q = "SELECT * FROM ".$tableName ." where ".$rowName.$ayrac.$value;
		 return $this->fetch($q);
	}
	
	/**
	*orm logic test step 2
	*/
	 
	public function where ($rowName,$ayrac,$value) {
		$where = ['row' =>$rowName,'ayrac' =>$ayrac,'value' =>$value];
		$this->where=$where;
		return $this;
	}
	
/*
*Chaining methods 
*/
  public function update2( $data) 
  {
	 $table=$this->tableName;
	 $where= $this->where;
	 $new_data=array();
	 	foreach ( $data as $key => $value ) {
			$new_data['key']=$key;
			$new_data['value']=$value;
		}


		/*
	UPDATE Customers SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' WHERE CustomerID = 1; //sql 
	UPDATE `access_users`   	SET        `telephone` = :telephone  WHERE `user_id` = :user_id 
		*/
		$sql="UPDATE `$table` SET `".$new_data['key']."` = :".$new_data['key']." WHERE `".$where['row']."` ".$where['ayrac']." :".$where['row']." ";
		$stmt = self::$dbMysql->prepare ( $sql );
	
			$stmt->bindValue(":".$new_data['key'], $new_data['value']);
			$stmt->bindValue(":".$where['row'], $where['value']);
		
		$stmt->execute ();
		
	  return $this;


  }
	
}
