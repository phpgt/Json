<?php
namespace GT\Json\Schema;

use GT\Json\JSONKvpObject;
use GT\Json\JSONObject;
use GT\Json\JSONPrimitive\JSONPrimitive;
use JsonSchema\Validator as JsonSchemaValidator;

class Validator {
	public function __construct(
		private ?JSONObject $schema = null,
	) {}

	public function validate(JSONObject $json):ValidationResult {
		if($this->schema) {
			$validator = new JsonSchemaValidator();
			$object = $json instanceof JSONPrimitive
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
