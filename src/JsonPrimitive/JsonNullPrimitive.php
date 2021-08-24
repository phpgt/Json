<?php
namespace Gt\Json\JsonPrimitive;

class JsonNullPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():mixed {
		return null;
	}
}
