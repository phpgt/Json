<?php
namespace Gt\Json\JsonPrimitive;

class JsonArrayPrimitive extends JsonPrimitive {
	/** @return mixed[] */
	public function getPrimitiveValue():array {
		return (array)$this->value;
	}
}