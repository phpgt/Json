<?php
namespace Gt\Json\JsonPrimitive;

use Gt\Json\JsonObject;

abstract class JsonPrimitive extends JsonObject {
	protected bool|int|float|string|array|null $value;

	abstract public function getPrimitiveValue();

	public function withPrimitiveValue(
		bool|int|float|string|array|null $value
	):static {
		$clone = clone $this;
		$clone->value = $value;
		return $clone;
	}
}