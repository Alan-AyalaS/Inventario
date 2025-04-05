<?php
class ProductData {
	public static $tablename = "product";

	public function ProductData(){
		$this->name = "";
		$this->price_in = "";
		$this->price_out = "";
		$this->unit = "";
		$this->user_id = "";
		$this->presentation = "0";
		$this->created_at = "NOW()";
	}

	public function getCategory(){ return CategoryData::getById($this->category_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (barcode,name,description,price_in,price_out,user_id,presentation,unit,category_id,inventary_min,created_at) ";
		$sql .= "value (\"$this->barcode\",\"$this->name\",\"$this->description\",\"$this->price_in\",\"$this->price_out\",$this->user_id,\"$this->presentation\",\"$this->unit\",$this->category_id,$this->inventary_min,NOW())";
		return Executor::doit($sql);
	}
	public function add_with_image(){
		$sql = "insert into ".self::$tablename." (barcode,image,name,description,price_in,price_out,user_id,presentation,unit,category_id,inventary_min) ";
		$sql .= "value (\"$this->barcode\",\"$this->image\",\"$this->name\",\"$this->description\",\"$this->price_in\",\"$this->price_out\",$this->user_id,\"$this->presentation\",\"$this->unit\",$this->category_id,$this->inventary_min)";
		return Executor::doit($sql);
	}


	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		// Primero eliminar todas las operaciones asociadas
		$operations = OperationData::getAllByProductId($this->id);
		foreach ($operations as $op) {
			$op->del();
		}
		
		// Luego eliminar el producto
		$sql = "delete from ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto ProductData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set barcode=\"$this->barcode\",name=\"$this->name\",price_in=\"$this->price_in\",price_out=\"$this->price_out\",unit=\"$this->unit\",presentation=\"$this->presentation\",category_id=$this->category_id,inventary_min=\"$this->inventary_min\",description=\"$this->description\",is_active=\"$this->is_active\",availability=\"$this->availability\" where id=$this->id";
		Executor::doit($sql);
	}

	public function updateAvailability($newAvailability) {
		$this->availability = $newAvailability;
		$sql = "update ".self::$tablename." set availability=$this->availability where id=$this->id";
		Executor::doit($sql);
	}

	public function del_category(){
		$sql = "update ".self::$tablename." set category_id=NULL where id=$this->id";
		Executor::doit($sql);
	}


	public function update_image(){
		$sql = "update ".self::$tablename." set image=\"$this->image\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());

	}



	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}


	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where id>=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}


	public static function getLike($p){
		$sql = "select * from ".self::$tablename." where barcode like '%$p%' or name like '%$p%' or id like '%$p%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}



	public static function getAllByUserId($user_id){
		$sql = "select * from ".self::$tablename." where user_id=$user_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getAllByCategoryId($category_id){
		$sql = "select * from ".self::$tablename." where category_id=$category_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos creados en el último mes
	 */
	public static function getLastMonth(){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos de una categoría creados en el último mes
	 */
	public static function getLastMonthByCategory($category_id){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos creados en la última semana
	 */
	public static function getLastWeek(){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos de una categoría creados en la última semana
	 */
	public static function getLastWeekByCategory($category_id){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos creados esta semana (desde el lunes)
	 */
	public static function getThisWeek(){
		$sql = "select * from ".self::$tablename." where YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos de una categoría creados esta semana (desde el lunes)
	 */
	public static function getThisWeekByCategory($category_id){
		$sql = "select * from ".self::$tablename." where YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos creados la semana pasada
	 */
	public static function getPreviousWeek(){
		$sql = "select * from ".self::$tablename." where YEARWEEK(created_at, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos de una categoría creados la semana pasada
	 */
	public static function getPreviousWeekByCategory($category_id){
		$sql = "select * from ".self::$tablename." where YEARWEEK(created_at, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos creados hoy
	 */
	public static function getToday(){
		// Construir consulta más explícita para depuración
		$sql = "select * from ".self::$tablename." where DATE(created_at) = CURDATE()";
		//echo "<!-- Consulta SQL getToday: ".$sql." -->";
		$query = Executor::doit($sql);
		$result = Model::many($query[0],new ProductData());
		//echo "<!-- Resultados recuperados: ".count($result)." -->";
		return $result;
	}
	
	/**
	 * Obtiene los productos de una categoría creados hoy
	 */
	public static function getTodayByCategory($category_id){
		// Construir consulta más explícita para depuración
		$sql = "select * from ".self::$tablename." where DATE(created_at) = CURDATE() AND category_id=$category_id";
		//echo "<!-- Consulta SQL getTodayByCategory: ".$sql." -->";
		$query = Executor::doit($sql);
		$result = Model::many($query[0],new ProductData());
		//echo "<!-- Resultados recuperados por categoría: ".count($result)." -->";
		return $result;
	}
	
	/**
	 * Obtiene los productos creados ayer
	 */
	public static function getYesterday(){
		$sql = "select * from ".self::$tablename." where DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/**
	 * Obtiene los productos de una categoría creados ayer
	 */
	public static function getYesterdayByCategory($category_id){
		$sql = "select * from ".self::$tablename." where DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos creados este mes
	 */
	public static function getThisMonth(){
		$sql = "select * from ".self::$tablename." where YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos de una categoría creados este mes
	 */
	public static function getThisMonthByCategory($category_id){
		$sql = "select * from ".self::$tablename." where YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos creados en los últimos 3 meses
	 */
	public static function getLast3Months(){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos de una categoría creados en los últimos 3 meses
	 */
	public static function getLast3MonthsByCategory($category_id){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos creados en los últimos 6 meses
	 */
	public static function getLast6Months(){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos de una categoría creados en los últimos 6 meses
	 */
	public static function getLast6MonthsByCategory($category_id){
		$sql = "select * from ".self::$tablename." where created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos creados este año
	 */
	public static function getThisYear(){
		$sql = "select * from ".self::$tablename." where YEAR(created_at) = YEAR(CURRENT_DATE())";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	/**
	 * Obtiene los productos de una categoría creados este año
	 */
	public static function getThisYearByCategory($category_id){
		$sql = "select * from ".self::$tablename." where YEAR(created_at) = YEAR(CURRENT_DATE()) AND category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}







}

?>