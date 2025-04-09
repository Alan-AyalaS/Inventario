<?php
class UserData {
	public static $tablename = "user";



	public function Userdata(){
		$this->name = "";
		$this->lastname = "";
		$this->email = "";
		$this->image = "";
		$this->password = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into user (name,lastname,username,email,is_admin,password,image,created_at) ";
		$sql .= "value (\"$this->name\",\"$this->lastname\",\"$this->username\",\"$this->email\",\"$this->is_admin\",\"$this->password\",\"$this->image\",NOW())";
		return Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto UserData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set ";
		$sql .= "name=\"$this->name\", ";
		$sql .= "lastname=\"$this->lastname\", ";
		$sql .= "username=\"$this->username\", ";
		$sql .= "email=\"$this->email\", ";
		$sql .= "image=\"$this->image\", "; 
		$sql .= "is_active=\"$this->is_active\", ";
		$sql .= "is_admin=\"$this->is_admin\", ";
		$sql .= "password=\"$this->password\" "; 
		$sql .= " where id=$this->id";
		
		Executor::doit($sql);
	}

	// Este método ya no es estrictamente necesario si update() maneja la contraseña,
	// pero lo dejamos por si se usa en otro lugar. Considerar refactorizar.
	public function update_passwd(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		
		// Debug info
		echo "<!-- SQL query (getById): " . $sql . " -->";
		echo "<!-- Raw query result (getById): ";
		$row = $query[0]->fetch_array();
		print_r($row);
		echo " -->";
		
		$found = null;
		if($row) {
			$data = new UserData();
			$data->id = $row['id'];
			$data->name = $row['name'];
			$data->lastname = $row['lastname'];
			$data->username = $row['username'];
			$data->email = $row['email'];
			$data->password = $row['password'];
			$data->is_active = $row['is_active'];
			$data->is_admin = $row['is_admin'];
			$data->image = $row['image'];
			$data->created_at = $row['created_at'];
			$found = $data;
			
			echo "<!-- Found user data (getById): ";
			print_r($found);
			echo " -->";
			echo "<!-- Image from DB (getById): " . $found->image . " -->";
		}
		
		return $found;
	}

	public static function getByMail($mail){
		$sql = "select * from ".self::$tablename." where email=\"$mail\"";
		$query = Executor::doit($sql);
		
		// Debug info
		echo "<!-- SQL query (getByMail): " . $sql . " -->";
		echo "<!-- Raw query result (getByMail): ";
		$row = $query[0]->fetch_array();
		print_r($row);
		echo " -->";
		
		$found = null;
		if($row) {
			$data = new UserData();
			$data->id = $row['id'];
			$data->name = $row['name'];
			$data->lastname = $row['lastname'];
			$data->username = $row['username'];
			$data->email = $row['email'];
			$data->password = $row['password'];
			$data->is_active = $row['is_active'];
			$data->is_admin = $row['is_admin'];
			$data->image = $row['image'];
			$data->created_at = $row['created_at'];
			$found = $data;
			
			echo "<!-- Found user data (getByMail): ";
			print_r($found);
			echo " -->";
			echo "<!-- Image from DB (getByMail): " . $found->image . " -->";
		}
		
		return $found;
	}


	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());

	}

	public static function getByUsername($username){
		$sql = "select * from ".self::$tablename." where username=\"$username\"";
		$query = Executor::doit($sql);
		
		// Debug info
		echo "<!-- SQL query: " . $sql . " -->";
		echo "<!-- Raw query result: ";
		$row = $query[0]->fetch_array();
		print_r($row);
		echo " -->";
		
		$found = null;
		if($row) {
			$data = new UserData();
			$data->id = $row['id'];
			$data->name = $row['name'];
			$data->lastname = $row['lastname'];
			$data->username = $row['username'];
			$data->email = $row['email'];
			$data->password = $row['password'];
			$data->is_active = $row['is_active'];
			$data->is_admin = $row['is_admin'];
			$data->image = $row['image'];
			$data->created_at = $row['created_at'];
			$found = $data;
			
			echo "<!-- Found user data: ";
			print_r($found);
			echo " -->";
			echo "<!-- Image from DB: " . $found->image . " -->";
		}
		
		return $found;
	}


}

?>