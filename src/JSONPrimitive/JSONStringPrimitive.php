<?php
namespace GT\Json\JSONPrimitive;

use GT\Json\JSONDecodeException;

class JSONStringPrimitive extends JSONPrimitive {
	public function getPrimitiveValue():string {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (string)$value;
	}
}
