<?php
use Gt\Json\JsonObject;
use Gt\Json\JsonObjectBuilder;

require __DIR__ . "/../vendor/autoload.php";

echo "Attempt 1:", PHP_EOL;

$jsonString = <<<JSON
{
	"logged_in_user_ids": [10521, 21042, 999991]
}
JSON;

$builder = new JsonObjectBuilder();
$json = $builder->fromJsonString($jsonString);

echo "Logged in users: ", implode(", ", $json->getArray("logged_in_user_ids", "int")), PHP_EOL;


echo "Attempt 2:", PHP_EOL;

$jsonString = <<<JSON
{
	"logged_in_user_ids": [10521, true, 999991]
}
JSON;

$builder = new JsonObjectBuilder();
$json = $builder->fromJsonString($jsonString);

echo "Logged in users: ", implode(", ", $json->getArray("logged_in_user_ids", "int")), PHP_EOL;

/* Example output:
Attempt 1:
Logged in users: 10521, 21042, 999991
Attempt 2:
Logged in users: PHP Fatal error:  Uncaught TypeError: Array index 1 must be of type int, boolean given
*/
