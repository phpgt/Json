<?php
namespace GT\Json;

use Gt\DataObject\DataObjectBuilder;
use GT\Json\JSONPrimitive\JSONArrayPrimitive;
use GT\Json\JSONPrimitive\JSONBoolPrimitive;
use GT\Json\JSONPrimitive\JSONFloatPrimitive;
use GT\Json\JSONPrimitive\JSONIntPrimitive;
use GT\Json\JSONPrimitive\JSONNullPrimitive;
use GT\Json\JSONPrimitive\JSONPrimitive;
use GT\Json\JSONPrimitive\JSONStringPrimitive;
use JsonException as NativeJsonException;
use stdClass;

class JSONObjectBuilder extends DataObjectBuilder {
	/**
	 * @param int<1, max> $depth
	 * @param int $flags
	 */
	public function __construct(
		private readonly int $depth = 512,
		private readonly int $flags = 0,
	) {
	}

	public function fromJsonString(string $jsonString):JSONObject {
		try {
			$json = json_decode(
				$jsonString,
				depth: $this->depth,
				flags: JSON_THROW_ON_ERROR | $this->flags,
			);
		}
		catch(NativeJsonException $exception) {
			throw new JSONDecodeException($exception->getMessage());
		}

		// Ensure $json is of the expected type
		if(!is_object($json) && !is_array($json) && !is_scalar($json) && $json !== null) {
			throw new JSONDecodeException("Invalid JSON structure");
		}

		return $this->fromJsonDecoded($json);
	}

	/**
	 * Create a JSONObject from a decoded JSON value.
	 * 
	 * @param object|array<int|string, mixed>|string|int|float|bool|null $jsonDecoded The decoded JSON value
	 * @return JSONObject The resulting JSONObject
	 */
	public function fromJsonDecoded(
		object|array|string|int|float|bool|null $jsonDecoded
	):JSONObject {
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
			is_null($jsonDecoded) => new JSONNullPrimitive(),
			is_bool($jsonDecoded) => new JSONBoolPrimitive(),
			is_int($jsonDecoded) => new JSONIntPrimitive(),
			is_float($jsonDecoded) => new JSONFloatPrimitive(),
			is_string($jsonDecoded) => new JSONStringPrimitive(),
			default => $this->asJSONKvpObject($jsonDecoded),
		};

		// Set primitive value if applicable
		if($jsonData instanceof JSONPrimitive) {
			$jsonData = $jsonData->withPrimitiveValue($jsonDecoded);
		}

		return $jsonData;
	}

	/**
	 * Process array data and convert it to a JSONArrayPrimitive.
	 * 
	 * @param array<int|string, mixed> $arrayData The array data to process
	 * @return JSONArrayPrimitive The resulting JSONArrayPrimitive
	 */
	private function processArrayData(array $arrayData): JSONArrayPrimitive {
		$processedArray = [];

		foreach($arrayData as $key => $value) {
			$processedArray[$key] = $this->processArrayElement($value);
		}

		$jsonData = new JSONArrayPrimitive();
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
			return $this->asJSONKvpObject($element);
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
	 * Create a JSONObject from an associative array.
	 * 
	 * @param array<string, mixed> $input The associative array to convert
	 * @return JSONObject The resulting JSONObject
	 * @throws JSONDecodeException If the JSON encoding fails
	 */
	public function fromAssociativeArray(array $input):JSONObject {
		$jsonString = json_encode($input);
		if($jsonString === false) {
			throw new JSONDecodeException("Failed to encode array to JSON");
		}
		return $this->fromJsonString($jsonString);
	}

	public function asJSONKvpObject(
		object $input,
	):JSONKvpObject {
		$kvp = new JSONKvpObject();

		foreach(get_object_vars($input) as $key => $value) {
			$kvp = $kvp->with($key, $value);
		}

		return $kvp;
	}

	public function fromFile(string $filePath):JSONObject {
		if(!is_file($filePath)) {
			throw new FileNotFoundException($filePath);
		}

		return self::fromJsonString(file_get_contents($filePath) ?: "");
	}
}
