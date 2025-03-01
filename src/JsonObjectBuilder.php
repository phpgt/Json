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

		return $this->fromJsonDecoded($json);
	}

	/**
	 * @param object|array<int, mixed>|string|int|float|bool|null $jsonDecoded
	 */
	public function fromJsonDecoded(
		object|array|string|int|float|bool|null $jsonDecoded
	):JsonObject {
		if(is_array($jsonDecoded)
		&& !is_int(key($jsonDecoded))) {
// The JSON could represent a primitive indexed array, but the json could have
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

	public function fromFile(string $filePath):JsonObject {
		if(!is_file($filePath)) {
			throw new FileNotFoundException($filePath);
		}

		return self::fromJsonString(file_get_contents($filePath));
	}

}
