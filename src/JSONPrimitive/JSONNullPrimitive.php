<?php
namespace GT\Json\JSONPrimitive;

class JSONNullPrimitive extends JSONPrimitive {
	public function getPrimitiveValue():mixed {
		return null;
	}
}
