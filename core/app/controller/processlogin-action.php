<?php

$_SESSION["user_id"]=$user->id;
$_SESSION["user_name"]=$user->name;
$_SESSION["user_lastname"]=$user->lastname;
$_SESSION["user_image"]=$user->image;
print "<script>window.location='index.php';</script>"; 