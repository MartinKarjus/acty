<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

/*
 * Added for convenience.
 * Resets table.
 * */

include_once '../db/Db.php';

http_response_code(200);

$database = new Db();
$conn = $database->connect();

$conn->query("DROP TABLE IF EXISTS organization CASCADE;");
$conn->query("CREATE TABLE organization (
    org_name varchar(255) PRIMARY KEY,
    parent_org_name varchar(255)
);");

echo json_encode(array("Message" => "DB reset"));

mysqli_close($conn)

?>