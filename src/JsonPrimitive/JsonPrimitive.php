<?php
namespace Gt\Json\JsonPrimitive;

use Gt\Json\JsonObject;

abstract class JsonPrimitive extends JsonObject {
	/** @var bool|int|float|string|array<int, mixed>|null */
	protected bool|int|float|string|array|null $value;

	abstract public function getPrimitiveValue():mixed;

	/**
	 * @param bool|int|float|string|array<int, mixed>|null $value
	 */
	public function withPrimitiveValue(
		bool|int|float|string|array|null $value
	):static {
		$clone = clone $this;
		$clone->value = $value;
		return $clone;
	}
}
