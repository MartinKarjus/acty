<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../db/Db.php';
include_once '../objects/Organization.php';

function invalid() {
    http_response_code(400);
    echo json_encode(array("message" => "Organization must have a unique name."));
}


$database = new Db();
$conn = $database->connect();

$organization = new Organization($conn);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->org_name)) {
    if($organization->saveOrganization($data)) {
        http_response_code("201");
        echo json_encode(array("message" => "Organization added.",
            "data" => json_encode($data)));
    } else {
        http_response_code("503");
        echo json_encode(array("message" => "Organization couldn't be added."));
    }
} else {
    invalid();
}
mysqli_close($conn)

?>
