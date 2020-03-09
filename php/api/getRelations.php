<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../db/Db.php';
include_once '../objects/Organization.php';



$database = new Db();
$conn = $database->connect();
$org = new Organization($conn);

$org->setOrgName(isset($_GET['name']) ? $_GET['name'] : die());

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$org->readOrg();

if($org -> getOrgName() != null) {
    echo json_encode($org->getRelations($page));
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Organization does not exist."));
}

mysqli_close($conn)

?>