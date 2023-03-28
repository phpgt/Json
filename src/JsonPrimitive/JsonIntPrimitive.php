<?php
namespace Gt\Json\JsonPrimitive;

class JsonIntPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():int {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (int)$value;
	}
}
