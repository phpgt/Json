<?php
namespace GT\Json\JsonPrimitive;

class JsonNullPrimitive extends JsonPrimitive {
	public function getPrimitiveValue():mixed {
		return null;
	}
}
