<?php
namespace GT\Json\JSONPrimitive;

class JSONIntPrimitive extends JSONPrimitive {
	public function getPrimitiveValue():int {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (int)$value;
	}
}
