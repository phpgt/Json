<?php
namespace Gt\Json\JsonPrimitive;

class JsonIntPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():int {
		return (int)$this->value;
	}
}