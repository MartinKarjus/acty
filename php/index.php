<?php

include_once('objects/Organization.php');
include_once('db/Db.php');

echo "service running";


$host = 'db';
$user = 'user';
$password = 'pass';
$db = 'acty_db';


$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    echo 'connection failed' . $conn->connect_error;
}

echo 'Connected to MySQL \n';


$result = $conn->query("select org_name, parent_org_name, 'sibling' AS relationship_type from organization");

//echo ' result: ' . $result;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo($row['org_name'] . " " . $row['relationship_type'] . "\n");
    }
} else {
    echo("no data");
}


$conn->close();
echo '<br>';



?>
