<?php
namespace core\lib;

class db  {
	protected $pdo = null;
	function __construct($ConnectNow = true,$DbName = false,$DbHost = false,$DbUser = false,$DbPass = false) {
		if($ConnectNow)
			$this->connect ($DbName,$DbHost,$DbUser,$DbPass);		
		return;	
	}	
	public function GetSQLConnection(){	
	   if($this->pdo == null) 
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
			if ($config['showSqlErrors'])
				$this->pdo->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		} catch ( \Exception $e ) {
			trigger_error ( 'Could not connect to the database.', E_USER_ERROR );
		}
		
		if ($this->pdo) {			
			return true;
		}
		
		return false;
	}
	
	public function query($query, array $myarr = null) {
		$result = $this->pdo->prepare ( $query );
		$result->execute ( $myarr );

		if ($result == false) {
		   
			// Log Error
		}
		return $result;
	}

	public function update(array $values, $table, $where = false, $limit = false, array $myarr = null) {
		if (count ( $values ) < 0)
			return false;
		$fields = [];
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

	public function insert(array $values, $table, array $myarr = null) {
		if (count ( $values ) < 0)
			return false;
		
		$stmt = "INSERT INTO `" . $table . "` (`" . implode ( array_keys ( $values ), "`, `" ) . "`) VALUES (" . implode ( $values, "," ) . ")";
		if ($this->query ( $stmt, $myarr )) {
			return true;
		} else
			return false;
	}
	
	public function select($fields, $table = false, $where = false, $orderby = false, $limit = false, array $myarr = null) {
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
	public function selectValues($fields, $table = false, $where = false, $orderby = false, $limit = false, array $myarr = null) {
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

	public function selectOne($fields, $table = false, $where = false, $orderby = false, array $myarr = null) {
		$result = $this->select ( $fields, $table, $where, $orderby, '1', $myarr );
		return $result [0];
	}

	public function selectOneValue($field, $table = false, $where = false,$order = false, array $myarr = null) {
		$result = $this->selectOne ( $field, $table, $where, $order, $myarr );
		return $result [$field];
	}
	
	public function delete($table, $where = false, $limit = false, array $myarr = null) {
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';
		$stmt = "DELETE FROM `" . $table . "`" . $where . $limit;
		if ($this->query ( $stmt, $myarr ))
			return true;
		else
			return false;
	}
	

	public function insertId() {
		return ( int ) $this->pdo->lastInsertId ();
	}
	public function error() {
		return $this->pdo->errorCode ();
	}	
	public function count($table, $where = '', array $myarr = null)
	{
	    return $this->selectOneValue('count(*)',$table,$where,false,$myarr);
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
}