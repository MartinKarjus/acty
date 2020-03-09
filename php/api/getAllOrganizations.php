<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

/*
 * Added for convenience.
 * Returns all existing organizations.
 * */

include_once '../db/Db.php';
include_once '../objects/Organization.php';

http_response_code(200);

$database = new Db();
$conn = $database->connect();
$org = new Organization($conn);

echo json_encode(array("organizations" => $org->getAllOrganizations()));


mysqli_close($conn)

?>