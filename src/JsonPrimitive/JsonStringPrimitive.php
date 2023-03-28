<?php
namespace Gt\Json\JsonPrimitive;

class JsonStringPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():string {
		return (string)$this->value;
	}
}
