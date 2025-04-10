<?php


// 10 de Octubre del 2014
// Model.php
// @brief agrego la clase Model para reducir las lineas de los modelos

class Model {

	public static function exists($modelname){
		$fullpath = self::getFullpath($modelname);
		$found=false;
		if(file_exists($fullpath)){
			$found = true;
		}
		return $found;
	}

	public static function getFullpath($modelname){
		return Core::$root."core/app/model/".$modelname.".php";
	}

	public static function many($query,$aclass){
		$cnt = 0;
		$array = array();
		while($r = $query->fetch_array()){
			$obj = new $aclass;
			$reflection = new ReflectionClass($aclass);
			foreach ($r as $key => $v) {
				if ($reflection->hasProperty($key)) {
					$property = $reflection->getProperty($key);
					$property->setAccessible(true);
					$property->setValue($obj, $v);
				}
			}
			$array[$cnt] = $obj;
			$cnt++;
		}
		return $array;
	}
	//////////////////////////////////
	public static function one($query,$aclass){
		$found = null;
		$obj = new $aclass;
		$reflection = new ReflectionClass($aclass);
		while($r = $query->fetch_array()){
			foreach ($r as $key => $v) {
				if ($reflection->hasProperty($key)) {
					$property = $reflection->getProperty($key);
					$property->setAccessible(true);
					$property->setValue($obj, $v);
				}
			}
			$found = $obj;
			break;
		}
		return $found;
	}

}



?>