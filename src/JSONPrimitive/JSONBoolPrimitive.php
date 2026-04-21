<?php
namespace GT\Json\JSONPrimitive;

class JSONBoolPrimitive extends JSONPrimitive {
	public function getPrimitiveValue():bool {
		return (bool)$this->value;
	}
}
