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
			
			// Convertir todos los valores a string para evitar problemas de tipo
			$processed_params = array();
			foreach($params as $param) {
				if(is_null($param)) {
					$processed_params[] = null;
				} else if(is_array($param)) {
					$processed_params[] = json_encode($param);
				} else {
					$processed_params[] = (string)$param;
				}
			}
			
			// Crear array de referencias
			$refs = array();
			$types = str_repeat('s', count($processed_params));
			$refs[] = &$types;
			
			for($i = 0; $i < count($processed_params); $i++) {
				$refs[] = &$processed_params[$i];
			}
			
			// Bind parameters usando referencias
			call_user_func_array(array($stmt, 'bind_param'), $refs);
			
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