<?php
namespace core\lib;
use core\main\libs;
class db extends libs{
	public $pdo = NULL;
	public $Result;
	function __construct($ConnectNow = true,$DbName = false,$DbHost = false,$DbUser = false,$DbPass = false) {
		parent::__construct();
		if($ConnectNow)
			$this->connect ($DbName,$DbHost,$DbUser,$DbPass);
		
		return;
		
	}
	
	public function GetSQLConnection(){	
	   if($this->pdo == NULL) 
	       $this->connect ();
	    return $this->pdo;
	}
	public function connect($DbName = false,$DbHost = false,$DbUser = false,$DbPass = false) {
		try {
		    $config = Config::getAll();
			if(!$DbName || !$DbHost || !$DbUser || !$DbPass)
				$this->pdo = new \PDO ( 'mysql:dbname=' . $config['DbName'] . ';host=' . $config['DbHost'], $config['DbUser'], $config['DbPass'] );
			else 
				$this->pdo = new \PDO ( 'mysql:dbname=' . $DbName . ';host=' . $DbHost, $DbUser, $DbPass );
			
			$this->pdo->exec ( 'SET NAMES utf8' );
			$this->pdo->setAttribute ( \PDO::ATTR_EMULATE_PREPARES, FALSE );
			if ($config['SqlErrorDetais'])
				$this->pdo->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		} catch ( \Exception $e ) {
			trigger_error ( 'Could not connect to the database.', E_USER_ERROR );
		}
		
		if ($this->pdo) {			
			return true;
		}
		
		return false;
	}
	
	public function query($query, array $myarr = NULL) {
		$result = $this->pdo->prepare ( $query );
		$result->execute ( $myarr );
		
		if ($result == false) {
		   
			// Log Error
		}
		$this->res = $result;
		return $result;
	}

	public function update(array $values, $table, $where = false, $limit = false, array $myarr = NULL) {
		if (count ( $values ) < 0)
			return false;
		$fields = array ();
		foreach ( $values as $field => $val )
			$fields [] = "`" . $field . "` = $val ";
		
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';
		
		$stmt = "UPDATE `" . $table . "` SET " . implode ( $fields, ", " ) . $where . $limit;
	
		if ($this->query ( $stmt, $myarr ))
			return true;
		else
			return false;
	}

	public function insert(array $values, $table, array $myarr = NULL) {
		if (count ( $values ) < 0)
			return false;
		
		foreach ( $values as $field => $val )
			$values [$field] = $val;
		
		$stmt = "INSERT INTO `" . $table . "` (`" . implode ( array_keys ( $values ), "`, `" ) . "`) VALUES (" . implode ( $values, "," ) . ")";
		if ($this->query ( $stmt, $myarr )) {
			return true;
		} else
			return false;
	}
	
	public function select($fields, $table = false, $where = false, $orderby = false, $limit = false, array $myarr = NULL) {
		if (is_array ( $fields ))
			$fields = "`" . implode ( $fields, "`, `" ) . "`";
		
		$orderby = ($orderby) ? " ORDER BY " . $orderby : '';
		$table = ($table) ? " FROM " . $table : '';
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';
		$stmt = "SELECT " . $fields . $table . $where . $orderby . $limit;
		$res = $this->query ( $stmt, $myarr );
		if ($res->rowCount () > 0) {
			return $res->fetchAll(\PDO::FETCH_ASSOC);
		} else
			return false;
	}
	public function selectValues($fields, $table = false, $where = false, $orderby = false, $limit = false, array $myarr = NULL) {
		if (is_array ( $fields ))
			$fields = "`" . implode ( $fields, "`, `" ) . "`";
	
		$orderby = ($orderby) ? " ORDER BY " . $orderby : '';
		$table = ($table) ? " FROM " . $table : '';
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';
		$stmt = "SELECT " . $fields . $table . $where . $orderby . $limit;
		$res = $this->query ( $stmt, $myarr );
		if ($res->rowCount () > 0) {
			return $res->fetchAll(\PDO::FETCH_NUM);
		} else
			return false;
	}

	public function selectOne($fields, $table = false, $where = false, $orderby = false, array $myarr = NULL) {
		$result = $this->select ( $fields, $table, $where, $orderby, '1', $myarr );
		
		return $result [0];
	}

	public function selectOneValue($field, $table = false, $where = false,$order = false, array $myarr = NULL) {
		$result = $this->selectOne ( $field, $table, $where, $order, $myarr );
		return $result [$field];
	}
	
	public function delete($table, $where = false, $limit = false, array $myarr = NULL) {
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';
		$stmt = "DELETE FROM `" . $table . "`" . $where . $limit;
		if ($this->query ( $stmt, $myarr ))
			return true;
		else
			return false;
	}
	

	public function fetchAssoc($query = false, array $myarr = NULL) {
		$this->resCalc ( $query, $myarr );
		$cal = $this->res;
		if ($query != false)
			$cal = $query;
		return $cal->fetch ( \PDO::FETCH_ASSOC );
	}

	public function fetchRow($query = false) {
		$this->resCalc ( $query );
		$cal = $this->res;
		if ($query != false)
			$cal = $query;
		return $cal->fetch ( \PDO::FETCH_NUM );
	}
	
	public function fetchOne($query = false) {
		list ( $result ) = $this->fetchRow ( $query );
		return $result;
	}

	public function insertId() {
		return ( int ) $this->pdo->lastInsertId ();
	}
	
	public function affectedRows() {
		return ( int ) $this->res->rowCount ();
		;
	}

	public function error() {
		return $this->pdo->errorCode ();
	}
	public function beginTransaction(){
		$this->pdo->beginTransaction();
	}
	
	public function commit(){
		$this->pdo->commit();
	}
	
	public function rollBack(){
		$this->pdo->rollBack();
	}
	
	public function GetUniqeID(){
	    $res = $this->pdo->query('SELECT uuid()');
	    $res = $res->fetch ( \PDO::FETCH_ASSOC );
	    return $res['uuid()'];
	}
	public function count($table, $where = '', array $myarr = NULL)
	{
	    return $this->selectOneValue('count(*)',$table,$where,false,$myarr);
	}
}