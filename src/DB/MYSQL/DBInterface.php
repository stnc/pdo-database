<?php

/**
 * SAVEAS FRAMEWORK
 *
 * Copyright (c) 2015
 *
 * db interface
 * @author Selman TUNÇ <selmantunc@gmail.com>
 * @copyright Copyright (c) 2015 SAVEAS YAZILIM
 * @link http://github.com/stnc
 * @version 2.0.0.1
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
interface DBInterface {
	

	function rows($sql, $array = array(), $fetchMode = 'array');
	

	function fetch($sql, $fetchMode = 'array');
	

	function insert($table, $data);
	

	function update($table, $data, $where);
	

	function delete($table, $where, $limit = 1);
	

	function lastInsertId();
	
	function error();
}
