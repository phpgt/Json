<?php
namespace Gt\Json\JsonPrimitive;

class JsonBoolPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():bool {
		return (bool)$this->value;
	}
}