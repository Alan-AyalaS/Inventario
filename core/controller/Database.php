<?php
class Database {
	public static $db;
	public static $con;
	public $user;
	public $pass;
	public $host;
	public $ddbb;
	function Database(){
		$this->user="if0_38719796";$this->pass="n1B0MJi5VqmPiC";$this->host="sql309.infinityfree.com";$this->ddbb="if0_38719796_inventario";
	}

	function connect(){
		$this->user="if0_38719796";$this->pass="n1B0MJi5VqmPiC";$this->host="sql309.infinityfree.com";$this->ddbb="if0_38719796_inventario";
		$con = new mysqli($this->host,$this->user,$this->pass,$this->ddbb);
		$con->query("set sql_mode=''");
		$con->set_charset("utf8mb4");
		return $con;
	}

	public static function getCon(){
		if(self::$con==null && self::$db==null){
			self::$db = new Database();
			self::$con = self::$db->connect();
		}
		return self::$con;
	}
	
}
?>
