<?php
namespace Gt\Json\Schema;

use Gt\Json\JsonObject;
use Gt\Json\JsonPrimitive\JsonPrimitive;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Helper;

class Validator {
	public function __construct(
		private ?JsonObject $schema = null,
	) {}

	public function validate(JsonObject $json):ValidationResult {
		if($this->schema) {
			$validator = new \Opis\JsonSchema\Validator(
				stop_at_first_error: false,
			);
			$opisSchema = Helper::toJSON($this->schema);
			$opisData = is_a($json, JsonPrimitive::class)
				? $json->getPrimitiveValue()
				: Helper::toJSON($json);
			$validationResult = $validator->validate($opisData, $opisSchema);

			if(!$validationResult->isValid()) {
				$errorFormatter = new ErrorFormatter();
				$error = $errorFormatter->formatFlat($validationResult->error());
				$error = array_filter($error, fn($i) => $i !== "Data must match schema");
				return new ValidationError(array_values($error));
			}
		}

		return new ValidationSuccess();
	}
}
