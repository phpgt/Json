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
use JsonException as NativeJsonException;
use stdClass;

class JsonObjectBuilder extends DataObjectBuilder {
	/**
	 * @param int<1, max> $depth
	 * @param int $flags
	 */
	public function __construct(
		private readonly int $depth = 512,
		private readonly int $flags = 0,
	) {
	}

	public function fromJsonString(string $jsonString):JsonObject {
		try {
			$json = json_decode(
				$jsonString,
				depth: $this->depth,
				flags: JSON_THROW_ON_ERROR | $this->flags,
			);
		}
		catch(NativeJsonException $exception) {
			throw new JsonDecodeException($exception->getMessage());
		}

		// Ensure $json is of the expected type
		if(!is_object($json) && !is_array($json) && !is_scalar($json) && $json !== null) {
			throw new JsonDecodeException("Invalid JSON structure");
		}

		return $this->fromJsonDecoded($json);
	}

	/**
	 * Create a JsonObject from a decoded JSON value.
	 * 
	 * @param object|array<int|string, mixed>|string|int|float|bool|null $jsonDecoded The decoded JSON value
	 * @return JsonObject The resulting JsonObject
	 */
	public function fromJsonDecoded(
		object|array|string|int|float|bool|null $jsonDecoded
	):JsonObject {
		// Handle associative arrays by converting to objects
		if(is_array($jsonDecoded) && !is_int(key($jsonDecoded))) {
			return $this->fromJsonDecoded((object)$jsonDecoded);
		}

		// Handle indexed arrays separately
		if(is_array($jsonDecoded)) {
			return $this->processArrayData($jsonDecoded);
		}

		// Handle scalar and object types
		$jsonData = match(true) {
			is_null($jsonDecoded) => new JsonNullPrimitive(),
			is_bool($jsonDecoded) => new JsonBoolPrimitive(),
			is_int($jsonDecoded) => new JsonIntPrimitive(),
			is_float($jsonDecoded) => new JsonFloatPrimitive(),
			is_string($jsonDecoded) => new JsonStringPrimitive(),
			default => $this->asJsonKvpObject($jsonDecoded),
		};

		// Set primitive value if applicable
		if($jsonData instanceof JsonPrimitive) {
			$jsonData = $jsonData->withPrimitiveValue($jsonDecoded);
		}

		return $jsonData;
	}

	/**
	 * Process array data and convert it to a JsonArrayPrimitive.
	 * 
	 * @param array<int|string, mixed> $arrayData The array data to process
	 * @return JsonArrayPrimitive The resulting JsonArrayPrimitive
	 */
	private function processArrayData(array $arrayData): JsonArrayPrimitive {
		$processedArray = [];

		foreach($arrayData as $key => $value) {
			$processedArray[$key] = $this->processArrayElement($value);
		}

		$jsonData = new JsonArrayPrimitive();
		return $jsonData->withPrimitiveValue($processedArray);
	}

	/**
	 * Process an individual element from an array.
	 * 
	 * @param mixed $element The element to process
	 * @return mixed The processed element
	 */
	private function processArrayElement(mixed $element): mixed {
		if($element instanceof stdClass) {
			return $this->asJsonKvpObject($element);
		}

		if(is_array($element)) {
			$nestedArray = [];
			foreach($element as $nestedKey => $nestedValue) {
				$nestedArray[$nestedKey] = $this->processArrayElement($nestedValue);
			}
			return $nestedArray;
		}

		return $element;
	}

	/**
	 * Create a JsonObject from an associative array.
	 * 
	 * @param array<string, mixed> $input The associative array to convert
	 * @return JsonObject The resulting JsonObject
	 * @throws JsonDecodeException If the JSON encoding fails
	 */
	public function fromAssociativeArray(array $input):JsonObject {
		$jsonString = json_encode($input);
		if($jsonString === false) {
			throw new JsonDecodeException("Failed to encode array to JSON");
		}
		return $this->fromJsonString($jsonString);
	}

	public function asJsonKvpObject(
		object $input,
	):JsonKvpObject {
		$kvp = new JsonKvpObject();

		foreach(get_object_vars($input) as $key => $value) {
			$kvp = $kvp->with($key, $value);
		}

		return $kvp;
	}
}
