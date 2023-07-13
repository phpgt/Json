<?php
namespace Gt\Json\JsonPrimitive;

use Gt\Json\JsonDecodeException;
use Gt\Json\JsonObject;

abstract class JsonPrimitive extends JsonObject {
	/** @var object|bool|int|float|string|array<int, mixed>|null */
	protected object|bool|int|float|string|array|null $value;

	abstract public function getPrimitiveValue():mixed;

	/** @param bool|int|float|string|array<int, mixed>|null $value */
	public function withPrimitiveValue(
		object|bool|int|float|string|array|null $value
	):static {
		$clone = clone $this;
		$clone->value = $value;
		return $clone;
	}
}
