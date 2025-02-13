<?php
namespace Gt\Json\Schema;

use Gt\Json\JsonObject;

class JsonDocument {
	public function __construct(
		private ?JsonObject $jsonObject = null,
	) {}

	public function __toString():string {
		if(is_null($this->jsonObject)) {
			return "";
		}

		return json_encode($this->jsonObject);
	}

	public function setJson(JsonObject $jsonObject):void {
		$this->jsonObject = $jsonObject;
	}

}
