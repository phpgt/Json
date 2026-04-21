<?php
namespace GT\Json\Schema;

use Gt\DataObject\DataObject;
use GT\Json\JSONKvpObject;
use GT\Json\JSONObject;
use GT\Json\JSONObjectBuilder;
use JsonSerializable;

class ValidationError extends ValidationResult implements JsonSerializable {
	/** @param array<string> $errorList */
	public function __construct(
		private JSONObject $schema,
		private array $errorList,
	) {}

	/** @return array<string> */
	public function getErrorList():array {
		return $this->errorList;
	}

	public function jsonSerialize():DataObject {
		$builder = new JSONObjectBuilder();
		$errorObject = $builder->fromAssociativeArray([
			"error" => "The provided object does not match the schema",
			"errorList" => $this->errorList,
			"schema" => $this->schema->jsonSerialize(),
		]);

		return $errorObject;
	}
}
