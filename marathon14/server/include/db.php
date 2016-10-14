<?php
class DBConnection
{
	public static $connection;

	private function __construct(){}
	private function __wakeup(){}
	private function __clone(){}

	public static function get_connection(){
		if(self::$connection === null){
			self::$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET));
		}
		return self::$connection;
	}
}

class DBManger{
	public static function execute($query){
		$connection = DBConnection::get_connection();
		$status = $connection->exec($query);
		return $status;
	}

	public static function query($query){
		$connection = DBConnection::get_connection();
		$get_data = $connection->query($query);
		$records = $get_data->fetchAll(PDO::FETCH_ASSOC);
		return $records;
	}

	public static function lastInsertId() {
		$connection = DBConnection::get_connection();
		return $connection->lastInsertId();
	}
}