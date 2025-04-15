<?php
require_once 'core/app/model/PersonData.php';

header('Content-Type: application/json');

if(isset($_GET['phone'])) {
    $phone = $_GET['phone'];
    $person = PersonData::getByPhone($phone);
    echo json_encode(['exists' => ($person != null)]);
} else {
    echo json_encode(['exists' => false]);
}
exit;
?> 