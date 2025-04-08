<?php
if(isset($_GET["action"]) && $_GET["action"]=="destroy"){
	session_destroy();
	print "<script>window.location='index.php';</script>";
}
?> 