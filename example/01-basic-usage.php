<?php
use Gt\Json\JsonObject;
use Gt\Json\JsonObjectBuilder;

require __DIR__ . "/../vendor/autoload.php";

$jsonString = <<<JSON
{
	"object": "balance",
	"available": [
		{
			"amount": 2217713,
			"currency": "cad",
			"source_types": {
				"bank_account": 0,
				"card": 2217713
			}
		},
		{
			"amount": 7254790,
			"currency": "gbp",
			"source_types": {
				"bank_account": 0,
				"card": 7254790
			}
		}
	]
}
JSON;

$builder = new JsonObjectBuilder();
$json = $builder->fromJsonString($jsonString);

echo "Type of object: ", $json->getString("object"), PHP_EOL;

/** @var JsonObject $available */
foreach($json->getArray("available") as $available) {
	echo PHP_EOL;
	echo "Currency: ", $available->getString("currency"), PHP_EOL;
	echo "Amount: ", number_format($available->getInt("amount") / 100), PHP_EOL;
}

/* Example output:
Type of object: balance

Currency: cad
Amount: 22,177

Currency: gbp
Amount: 72,548
*/
