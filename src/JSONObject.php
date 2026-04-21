<?php
namespace GT\Json;

use Gt\DataObject\DataObject;
use GT\Json\JSONPrimitive\JSONPrimitive;
use Stringable;

abstract class JSONObject extends DataObject implements Stringable {
	public function __toString():string {
		return json_encode($this, JSON_THROW_ON_ERROR);
	}

	public function jsonSerialize():mixed {
		if($this instanceof JSONPrimitive) {
			return $this->getPrimitiveValue();
		}

		return parent::jsonSerialize();
	}
}
