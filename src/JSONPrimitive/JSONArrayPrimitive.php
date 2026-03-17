<?php
namespace GT\Json\JSONPrimitive;

class JSONArrayPrimitive extends JSONPrimitive {
	/** @return array<int, mixed> */
	public function getPrimitiveValue():array {
		return (array)$this->value;
	}
}
