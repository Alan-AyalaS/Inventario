<?php

class Executor {

	public static function doit($sql, $params = null){
		$con = Database::getCon();
		if(Core::$debug_sql){
			print "<pre>".$sql."</pre>";
		}
		
		if($params !== null) {
			$stmt = $con->prepare($sql);
			if($stmt === false) {
				return array(false, $con->error);
			}
			
			$types = str_repeat('s', count($params));
			$stmt->bind_param($types, ...$params);
			
			$result = $stmt->execute();
			if($result === false) {
				return array(false, $stmt->error);
			}
			
			return array($result, $con->insert_id);
		} else {
			$result = $con->query($sql);
			if($result === false) {
				return array(false, $con->error);
			}
			return array($result, $con->insert_id);
		}
	}
}
?>