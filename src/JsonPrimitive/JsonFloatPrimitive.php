<?php
namespace Gt\Json\JsonPrimitive;

class JsonFloatPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():float {
		return (float)$this->value;
	}
}