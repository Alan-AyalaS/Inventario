<?php
class PersonData {
	public static $tablename = "person";

	// Declarar todas las propiedades explícitamente
	public $id;
	public $name;
	public $lastname;
	public $email1;
	public $phone1;
	public $address1;
	public $city;
	public $state;
	public $zip_code;
	public $created_at;
	public $image;
	public $kind;
	public $is_active;
	public $user_id;
	public $email2;
	public $phone2;
	public $address2;

	public function PersonData(){
		$this->name = "";
		$this->lastname = "";
		$this->email1 = "";
		$this->phone1 = "";
		$this->address1 = "";
		$this->city = "";
		$this->state = "";
		$this->zip_code = "";
		$this->created_at = "NOW()";
		$this->image = "";
		$this->kind = "";
		$this->is_active = "1";
		$this->user_id = "";
		$this->email2 = "";
		$this->phone2 = "";
		$this->address2 = "";
	}

	public function add_client(){
		$sql = "insert into person (name,lastname,address1,city,state,zip_code,email1,phone1,kind,created_at) ";
		$sql .= "value (\"$this->name\",\"$this->lastname\",\"$this->address1\",\"$this->city\",\"$this->state\",\"$this->zip_code\",\"$this->email1\",\"$this->phone1\",1,NOW())";
		Executor::doit($sql);
	}

	public function add_provider(){
		$sql = "insert into person (name,lastname,address1,city,state,zip_code,email1,phone1,kind,created_at) ";
		$sql .= "value (\"$this->name\",\"$this->lastname\",\"$this->address1\",\"$this->city\",\"$this->state\",\"$this->zip_code\",\"$this->email1\",\"$this->phone1\",2,NOW())";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto PersonData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",email1=\"$this->email1\",address1=\"$this->address1\",city=\"$this->city\",state=\"$this->state\",zip_code=\"$this->zip_code\",lastname=\"$this->lastname\",phone1=\"$this->phone1\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_client(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",email1=\"$this->email1\",address1=\"$this->address1\",city=\"$this->city\",state=\"$this->state\",zip_code=\"$this->zip_code\",lastname=\"$this->lastname\",phone1=\"$this->phone1\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_provider(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",email1=\"$this->email1\",address1=\"$this->address1\",lastname=\"$this->lastname\",phone1=\"$this->phone1\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_passwd(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		$found = null;
		$data = new PersonData();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->name = $r['name'];
			$data->lastname = $r['lastname'];
			$data->address1 = $r['address1'];
			$data->city = $r['city'];
			$data->state = $r['state'];
			$data->zip_code = $r['zip_code'];
			$data->phone1 = $r['phone1'];
			$data->email1 = $r['email1'];
			$data->created_at = $r['created_at'];
			$found = $data;
			break;
		}
		return $found;
	}

	public static function getByPhone($phone){
		$sql = "select * from ".self::$tablename." where phone1=\"$phone\"";
		$query = Executor::doit($sql);
		$found = null;
		$data = new PersonData();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->name = $r['name'];
			$data->lastname = $r['lastname'];
			$data->address1 = $r['address1'];
			$data->city = $r['city'];
			$data->state = $r['state'];
			$data->zip_code = $r['zip_code'];
			$data->phone1 = $r['phone1'];
			$data->email1 = $r['email1'];
			$data->created_at = $r['created_at'];
			$found = $data;
			break;
		}
		return $found;
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new PersonData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->lastname = $r['lastname'];
			$array[$cnt]->email = $r['email1'];
			$array[$cnt]->username = $r['username'];
			$array[$cnt]->phone1 = $r['phone1'];
			$array[$cnt]->address1 = $r['address1'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}

	public static function getClients(){
		$sql = "select * from ".self::$tablename." where kind=1 order by name,lastname";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new PersonData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->lastname = $r['lastname'];
			$array[$cnt]->email1 = $r['email1'];
			$array[$cnt]->phone1 = $r['phone1'];
			$array[$cnt]->address1 = $r['address1'];
			$array[$cnt]->city = $r['city'];
			$array[$cnt]->state = $r['state'];
			$array[$cnt]->zip_code = $r['zip_code'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}


	public static function getProviders(){
		$sql = "select * from ".self::$tablename." where kind=2 order by name,lastname";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new PersonData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->lastname = $r['lastname'];
			$array[$cnt]->email1 = $r['email1'];
			$array[$cnt]->phone1 = $r['phone1'];
			$array[$cnt]->address1 = $r['address1'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}

	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new PersonData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->mail = $r['mail'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}


}

?>