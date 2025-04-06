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
		return Executor::doit($sql);
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

	public function update(){
		$sql = "update ".self::$tablename." set barcode=\"$this->barcode\",name=\"$this->name\",price_in=\"$this->price_in\",price_out=\"$this->price_out\",unit=\"$this->unit\",presentation=\"$this->presentation\",category_id=$this->category_id,inventary_min=$this->inventary_min where id=$this->id";
		return Executor::doit($sql);
	}

	public function update_with_image(){
		$sql = "update ".self::$tablename." set barcode=\"$this->barcode\",image=\"$this->image\",name=\"$this->name\",price_in=\"$this->price_in\",price_out=\"$this->price_out\",unit=\"$this->unit\",presentation=\"$this->presentation\",category_id=$this->category_id,inventary_min=$this->inventary_min where id=$this->id";
		return Executor::doit($sql);
	}

	public function updateAvailability($q){
		$sql = "update ".self::$tablename." set availability=$q where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());
	}

	public static function getAll($order = "desc"){
		$sql = "select * from ".self::$tablename." order by created_at $order";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getAllByCategoryId($category_id){
		$sql = "select * from ".self::$tablename." where category_id=$category_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where id<=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%' or id like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getTotalAvailability($product_id) {
		$sql = "SELECT SUM(CASE WHEN operation_type_id = 1 THEN q ELSE -q END) as total 
				FROM operation 
				WHERE product_id = $product_id";
		$query = Executor::doit($sql);
		$result = $query[0]->fetch_assoc();
		return $result['total'] ?? 0;
	}

	public static function getAvailabilityBySize($product_id, $talla) {
		$sql = "SELECT SUM(CASE WHEN operation_type_id = 1 THEN q ELSE -q END) as total 
				FROM operation 
				WHERE product_id = $product_id AND talla = '$talla'";
		$query = Executor::doit($sql);
		$result = $query[0]->fetch_assoc();
		return $result['total'] ?? 0;
	}
}
?>