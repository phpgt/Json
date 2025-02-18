<?php
namespace Gt\Json\Schema;

class ValidationError extends ValidationResult {
	/** @param array<string> $errorList */
	public function __construct(private array $errorList) {}

	/** @return array<string> */
	public function getErrorList():array {
		return $this->errorList;
	}
}
