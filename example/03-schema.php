<?php
// The JSON we will validate. Notice that one of the array keys is not a string,
// and the "colour" property is misspelled.
use Gt\Json\JsonObjectBuilder;
use Gt\Json\Schema\ValidationError;
use Gt\Json\Schema\Validator;

require __DIR__ . "/../vendor/autoload.php";

$jsonString = <<<JSON
{
	"name": "Cody",
	"colr": "orange",
	"food": [
		"Biscuits",
		"Mushrooms",
		12345
	]
}
JSON;

$schemaString = <<<JSON
{
	"\$schema": "https://json-schema.org/draft/2020-12/schema",
	"title": "Simple object schema",
	"type": "object",
	"properties": {
		"name": {
			"type": "string",
			"minLength": 1
		},
		"colour": {
			"type": "string"
		},
		"food": {
			"type": "array",
			"items": {
				"type": "string"
			}
		}
	},
	"required": [
		"name",
		"colour",
		"food"
	],
	"additionalProperties": false
}
JSON;

$builder = new JsonObjectBuilder();

$schema = $builder->fromJsonString($schemaString);
$json = $builder->fromJsonString($jsonString);

$validator = new Validator($schema);
$result = $validator->validate($json);

if($result instanceof ValidationError) {
	echo "Error validating JSON!", PHP_EOL;

	foreach($result->getErrorList() as $propertyName => $errorString) {
		echo "$propertyName: $errorString", PHP_EOL;
	}
}
else {
	echo "Everything is OK!", PHP_EOL;
}
