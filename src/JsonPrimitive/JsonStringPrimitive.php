<?php
namespace GT\Json\JsonPrimitive;

use GT\Json\JsonDecodeException;

class JsonStringPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():string {
		/** @var bool|int|float|string|null $value */
		$value = $this->value;
		return (string)$value;
	}
}
