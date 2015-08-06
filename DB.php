<?php

namespace DB;

/**
 * SAVEAS FRAMEWORK
 *
 * Copyright (c) 2015
 *
 * @author Selman TUNÇ <selmantunc@gmail.com>
 * @link http://github.com/stnc
 * @version 2.0.0.1
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
use \PDO;

class MYSQL extends PDO implements \DBInterface {
	public static $db = false;
	function __construct() {
		if (self::$db === false) {
			$this->connect ();
		}
	}
	/**
	 * pdo connector
	 */
	private function connect() {
		$dsn = DB_TYPE . ":dbname=" . DB_NAME . ";host=" . DB_HOST;
		try {
			self::$db = new PDO ( $dsn, DB_USER, DB_PASS, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' 
			) );
			self::$db->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $e ) {
			
			// your log handler
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
	 *          $db->rows($q);
	 */
	public function rows($sql, $array = array(), $fetchMode = 'array') {
		if ($fetchMode == 'array') {
			$fetchMode = PDO::FETCH_ASSOC;
		} else if ($fetchMode == 'object') {
			$fetchMode = PDO::FETCH_OBJ;
		}
		
		$stmt = self::$db->prepare ( $sql );
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
	 *          $db->fetch($q);
	 *         
	 */
	public function fetch($sql, $fetchMode = 'array') {
		if ($fetchMode == 'array') {
			$fetchMode = PDO::FETCH_ASSOC;
		} else if ($fetchMode == 'object') {
			$fetchMode = PDO::FETCH_OBJ;
		}
		
		$stmt = self::$db->prepare ( $sql );
		
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
	 *        	$this->querys($q)
	 */
	public function querys($sql) {
		$stmt = self::$db->prepare ( $sql );
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
		
		$stmt = self::$db->prepare ( "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)" );
		
		foreach ( $data as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		$stmt->execute ();
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
		
		$stmt = self::$db->prepare ( $s );
		
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
	 *          return $db->delete("users", $where);
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
		$stmt = self::$db->prepare ( $sql );
		
		foreach ( $where as $key => $value ) {
			$stmt->bindValue ( ":$key", $value );
		}
		
		$stmt->execute ();
	}
	
	/**
	 * son eklenenin id numarası
	 */
	function lastID() {
		return $this->lastInsertId ();
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
		return $this->exec ( "TRUNCATE TABLE $table" );
	}
}
