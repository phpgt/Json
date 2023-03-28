<?php
namespace Gt\Json\JsonPrimitive;

class JsonFloatPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():float {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (float)$value;
	}
}
