<?php
namespace Gt\Json;

use Gt\DataObject\DataObjectBuilder;
use Gt\Json\JsonPrimitive\JsonArrayPrimitive;
use Gt\Json\JsonPrimitive\JsonBoolPrimitive;
use Gt\Json\JsonPrimitive\JsonFloatPrimitive;
use Gt\Json\JsonPrimitive\JsonIntPrimitive;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;
use Gt\Json\JsonPrimitive\JsonPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use stdClass;

class JsonObjectBuilder extends DataObjectBuilder {
	public function fromJsonString(string $jsonString):JsonObject {
		$json = json_decode($jsonString);
		return $this->fromJsonDecoded($json);
	}

	public function fromJsonDecoded(
		object|array|string|int|float|bool|null $jsonDecoded
	):JsonObject {
		if(is_array($jsonDecoded)
		&& !is_int(key($jsonDecoded))) {
// The JSON could represent an primitive indexed array, but the json could have
// been decoded as an associative array too. Deal with associative arrays first.
			$jsonData = $this->fromJsonDecoded(
				(object)$jsonDecoded
			);
		}
		elseif(is_null($jsonDecoded)) {
			$jsonData = new JsonNullPrimitive();
		}
		elseif(is_bool($jsonDecoded)) {
			$jsonData = new JsonBoolPrimitive();
		}
		elseif(is_int($jsonDecoded)) {
			$jsonData = new JsonIntPrimitive();
		}
		elseif(is_float($jsonDecoded)) {
			$jsonData = new JsonFloatPrimitive();
		}
		elseif(is_string($jsonDecoded)) {
			$jsonData = new JsonStringPrimitive();
		}
		elseif(is_array($jsonDecoded)) {
			array_walk_recursive($jsonDecoded, function(&$element) {
				if($element instanceof StdClass) {
					$element = $this->fromObject(
						$element,
						JsonKvpObject::class
					);
				}
			});
			$jsonData = new JsonArrayPrimitive();
		}
		else {
			/** @var JsonKvpObject $jsonData */
			$jsonData = $this->fromObject(
				$jsonDecoded,
				JsonKvpObject::class
			);
		}

		if($jsonData instanceof JsonPrimitive) {
			$jsonData = $jsonData->withPrimitiveValue($jsonDecoded);
		}

		return $jsonData;
	}
}