<?php
namespace Gt\Json;

use Gt\DataObject\DataObjectBuilder;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;

class JsonObjectBuilder extends DataObjectBuilder {
	public function fromJsonString(string $jsonString):JsonObject {
		$json = json_decode($jsonString);
		return $this->fromJsonDecoded($json);
	}

	public function fromJsonDecoded(
		object|array|string|int|float|bool|null $jsonDecoded
	):JsonObject {
		if(is_object($jsonDecoded)) {
			/** @var JsonKvpObject $jsonData */
			$jsonData = $this->fromObject(
				$jsonDecoded,
				JsonKvpObject::class
			);
		}
		elseif(is_array($jsonDecoded)) {
// The JSON could represent an indexed array, but the json could have been
// decoded as an associative array too. Deal with both outcomes here.
			if(is_int(key($jsonDecoded))) {

			}
			else {
				$jsonData = $this->fromJsonDecoded(
					(object)$jsonDecoded
				);
			}
		}
		elseif(is_null($jsonDecoded)) {
			$jsonData = new JsonNullPrimitive();
		}

		return $jsonData;
	}
}