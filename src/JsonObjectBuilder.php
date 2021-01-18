<?php
namespace Gt\Json;

use Gt\DataObject\DataObjectBuilder;

class JsonObjectBuilder extends DataObjectBuilder {
	public function fromJsonString(string $jsonString):JsonObject {
		$json = json_decode($jsonString);
		return $this->fromJsonDecoded($json);
	}

	public function fromJsonDecoded(mixed $jsonDecoded):JsonObject {
		if(is_object($jsonDecoded)) {
			/** @var JsonKvpObject $jsonData */
			$jsonData = $this->fromObject(
				$jsonDecoded,
				JsonKvpObject::class
			);
		}

		return $jsonData;
	}
}