<?php
namespace Gt\Json\Schema;

use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObject;
use Gt\Json\JsonPrimitive\JsonPrimitive;

class Validator {
	public function __construct(
		private ?JsonKvpObject $schema = null,
	) {}

	public function validate(JsonObject $json):ValidationResult {
		if($this->schema) {
			$validator = new \JsonSchema\Validator();
			$object = $json instanceof JsonPrimitive
				? $json->getPrimitiveValue()
				: $json->asObject();
			$validator->validate($object, $this->schema->asObject());

			if(!$validator->isValid()) {
				$errorList = [];
				foreach($validator->getErrors() as $error) {
					$errorList[$error["pointer"] ?: "/"] = $error["message"];
				}

				ksort($errorList);
				return new ValidationError($this->schema, $errorList);
			}
		}

		return new ValidationSuccess();
	}
}
