<?php

header("Content-Type: application/json");

if (strtolower($_SERVER['REQUEST_METHOD']) != "post") {
    http_response_code(400);
    die(json_encode(array("success" => false, "message" => "Request method is not valid")));
}

if (
    !isset($_POST['route']) ||
    !isset($_POST['total']) ||
    !isset($_POST['start_date']) ||
    empty($_POST['route']) ||
    empty($_POST['total']) ||
    empty($_POST['start_date'])
) {
    http_response_code(400);
    die(json_encode(array("success" => false, "message" => "Data not valid")));
}

$route = $_POST['route'];
$total = $_POST['total'];
$start_date = $_POST['start_date'];

try {
    $test = json_decode($route);

    if (!is_array($test)) {
        throw new Exception("No array");
    }

    if (count($test) <= 0) {
        throw new Exception("Route is to short");
    }
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(array("success" => false, "message" => "Route data is not valid JSON array, or the route is to short (less then or equal to 0)")));
}

if (!is_numeric($total)) {
    http_response_code(400);
    die(json_encode(array("success" => false, "message" => "Total data is na valid float")));
}

$total = (float) $total;
$route = (string) $route;
$start_date = (string) $start_date;

$pdo = new PDO("mysql:dbname=km;hostname=localhost", "km", "Berkel1997");
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $pdo->prepare("INSERT INTO routes (date, kms, route, start_date) VALUES (:date, :total, :route, :start);");
try {
    $stmt->execute(array(
        ":date" => date('Y-m-d H:i:s'),
        ":total" => $total,
        ":route" => $route,
        ":start" => $start_date
    ));
} catch(PDOException $e) {
    var_dump($e);exit;
}


echo json_encode(array("success" => true, "message" => "Route is saved!"));