<?php
namespace GT\Json\JsonPrimitive;

class JsonBoolPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():bool {
		return (bool)$this->value;
	}
}
