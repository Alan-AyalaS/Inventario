<?php
@session_start(); // Suprimir notice si la sesión ya está activa

// define('LBROOT',getcwd()); // LegoBox Root ... the server root
// include("core/controller/Database.php");

if(!isset($_SESSION["user_id"])) {
	$usernameOrEmail = $_POST['username'] ?? null;
	$password = $_POST['password'] ?? null;

	if (!$usernameOrEmail || !$password) {
		$_SESSION["login_error"] = "Usuario y contraseña requeridos.";
		header("Location: index.php?view=login");
		exit();
	}

	$base = new Database();
	$con = $base->connect();

    // 1. Buscar al usuario por username o email
    $escapedUsernameOrEmail = $con->real_escape_string($usernameOrEmail);
    $sql = "SELECT * FROM user WHERE (username = '{$escapedUsernameOrEmail}' OR email = '{$escapedUsernameOrEmail}')";
	$query = $con->query($sql);

	$foundUser = null;
	if ($query && $query->num_rows == 1) {
		$foundUser = $query->fetch_assoc();
	}

	// 2. Verificar si se encontró el usuario y si está activo
	if ($foundUser && $foundUser['is_active'] == 1) {
        
        $passwordMatches = false;
        $needsRehash = false;
        $storedHash = $foundUser['password'];
        $password = $_POST['password'] ?? null;

        // Preparar array para debug en sesión
        $debugInfo = [
            'message' => 'Verificando contraseña',
            'input_password' => $password,
            'stored_hash' => $storedHash,
            'verify_result' => null,
            'sha1_md5_result' => null,
            'needs_rehash' => null
        ];

        // 3. Intentar verificar con el método moderno (password_verify)
		if (password_verify($password, $storedHash)) {
            $debugInfo['verify_result'] = true;
            $passwordMatches = true;
        }
        // 4. Si falla, intentar verificar con el método antiguo (sha1(md5())) - Para migración
        else {
            $debugInfo['verify_result'] = false;
            $calculated_old_hash = sha1(md5($password));
            $debugInfo['sha1_md5_result'] = ($storedHash == $calculated_old_hash);
             if ($storedHash == $calculated_old_hash) {
                 $passwordMatches = true;
                 $needsRehash = true;
                 $debugInfo['needs_rehash'] = true;
             } else {
                 $debugInfo['needs_rehash'] = false;
             }
        }
        
        // Guardar debug info ANTES de cualquier redirección por fallo
        $_SESSION['login_debug_info'] = $debugInfo;

        // 5. Si la contraseña coincide (por cualquier método)
        if ($passwordMatches) {
            
            // 6. Si se usó el método antiguo, actualizar el hash en la BD AHORA
            if ($needsRehash) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $userId = $foundUser['id'];
                $updateSql = "UPDATE user SET password = '" . $con->real_escape_string($newHash) . "' WHERE id = {$userId}";
                $con->query($updateSql); // Ejecutar actualización del hash
            }

            // 7. Contraseña correcta: Iniciar sesión
            $_SESSION['user_id'] = $foundUser['id'];
            $_SESSION['user_name'] = $foundUser['username'];
            $_SESSION['user_image'] = $foundUser['image'];

            // Limpiar debug y error en caso de ÉXITO
            unset($_SESSION['login_debug_info']);
            unset($_SESSION["login_error"]);
            header("Location: index.php?view=home");
            exit();
        } else {
            // Contraseña incorrecta - Mostrar debug y detener
             $_SESSION["login_error"] = "Contraseña incorrecta."; // Aún lo guardamos por si acaso
             echo "<h1>Error de Login (DEBUG)</h1>";
             echo "<p><strong>Error:</strong> Contraseña incorrecta.</p>";
             echo "<p><strong>Debug Info (var_dump):</strong></p><pre>";
             var_dump($debugInfo); // Usar var_dump para ver tipos y valores booleanos
             echo "</pre>";
             die(); // Detener ejecución aquí
            // header("Location: index.php?view=login"); // Comentado temporalmente
            // exit(); // Comentado temporalmente
        }
	} else {
		// Usuario no encontrado o inactivo - Mostrar debug y detener
        $error_message = "";
        if ($foundUser && $foundUser['is_active'] == 0) {
             $error_message = "El usuario está inactivo.";
        } else {
             $error_message = "Usuario o Email no encontrado.";
        }
        $_SESSION["login_error"] = $error_message; // Aún lo guardamos
        // Preparar info debug para este caso
        $debugInfo = [
             'message' => 'Usuario no encontrado o inactivo',
             'input_user' => $usernameOrEmail,
             'found_user_data' => $foundUser // Puede ser null
         ];
         echo "<h1>Error de Login (DEBUG)</h1>";
         echo "<p><strong>Error:</strong> " . htmlspecialchars($error_message) . "</p>";
         echo "<p><strong>Debug Info:</strong></p><pre>";
         print_r($debugInfo);
         echo "</pre>";
         die(); // Detener ejecución aquí
        // header("Location: index.php?view=login"); // Comentado temporalmente
        // exit(); // Comentado temporalmente
	}

} else {
	// Ya está logueado, redirigir a home
	header("Location: index.php?view=home");
	exit();
}
?>