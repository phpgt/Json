<?php
namespace Gt\Json\Schema;

use Gt\DataObject\DataObject;
use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObject;
use Gt\Json\JsonObjectBuilder;
use JsonSerializable;

class ValidationError extends ValidationResult implements JsonSerializable {
	/** @param array<string> $errorList */
	public function __construct(
		private JsonObject $schema,
		private array $errorList,
	) {}

	/** @return array<string> */
	public function getErrorList():array {
		return $this->errorList;
	}

	public function jsonSerialize():DataObject {
		$builder = new JsonObjectBuilder();
		$errorObject = $builder->fromAssociativeArray([
			"error" => "The provided object does not match the schema",
			"errorList" => $this->errorList,
			"schema" => $this->schema->jsonSerialize(),
		]);

		return $errorObject;
	}
}
