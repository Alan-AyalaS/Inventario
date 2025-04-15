<?php
header('Content-Type: application/json');

if(isset($_GET['phone'])) {
    require_once '../../model/PersonData.php';
    $phone = $_GET['phone'];
    $person = PersonData::getByPhone($phone);
    
    echo json_encode(['exists' => ($person != null)]);
    exit;
}
echo json_encode(['exists' => false]);
exit;
?> 