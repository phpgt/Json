<?php
namespace Gt\Json;

use Gt\DataObject\DataObject;
use Gt\Json\JsonPrimitive\JsonPrimitive;
use Stringable;

abstract class JsonObject extends DataObject implements Stringable {
	public function __toString():string {
		return json_encode($this, JSON_THROW_ON_ERROR);
	}

	public function jsonSerialize():mixed {
		if($this instanceof JsonPrimitive) {
			return $this->getPrimitiveValue();
		}

		return parent::jsonSerialize();
	}
}
