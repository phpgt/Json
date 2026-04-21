<?php
namespace GT\Json\JSONPrimitive;

use GT\Json\JSONDecodeException;
use GT\Json\JSONObject;

abstract class JSONPrimitive extends JSONObject {
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
