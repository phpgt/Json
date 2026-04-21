<?php
namespace GT\Json\JSONPrimitive;

class JSONFloatPrimitive extends JSONPrimitive {
	public function getPrimitiveValue():float {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (float)$value;
	}
}
