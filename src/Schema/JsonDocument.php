<?php
namespace Gt\Json\Schema;

use Gt\Json\JsonErrorCustomPropertyNameException;
use Gt\Json\JsonErrorStateException;
use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObject;
use Gt\Json\JsonTypeException;

class JsonDocument {
	private bool $hasError = false;

	public function __construct(
		private ?JsonObject $jsonObject = null,
	) {}

	public function __toString():string {
		if(is_null($this->jsonObject)) {
			return "";
		}

		return json_encode($this->jsonObject) ?: "";
	}

	public function setObject(JsonObject $jsonObject):void {
		$this->jsonObject = $jsonObject;
	}

	/**
	 * Set a value in the document using dot notation for nested objects.
	 *
	 * @param string $key The key to set, can use dot notation for nested objects
	 * @param mixed $value The value to set
	 * @throws JsonTypeException If the document object is not a JsonKvpObject
	 */
	public function set(string $key, mixed $value):void {
		if ($this->hasError) {
			throw new JsonErrorStateException();
		}

		$this->ensureJsonKvpObject();

		if(!str_contains($key, ".")) {
			$this->setSimpleKey($key, $value);
			return;
		}

		$this->setNestedKey($key, $value);
	}

	/**
	 * Clear all set properties and set an error state. No additional JSON
	 * properties may be set once an error is set.
	 *
	 * @param string $message The message to be set
	 * @param array|null $context An optional array containing error context to be returned
	 * @param string $property Optional property name for the error message
	 * @param string $contextProperty Optional property name for the error context array
	 *
	 * @throws JsonErrorCustomPropertyNameException If clashing property names are provided
	 */
	public function error(
		string $message,
		?array $context = null,
		string $property = "error",
		string $contextProperty = "errorContext"
	):void {
		$this->jsonObject = null;
		$this->set($property, $message);

		if ($context) {
			if ($property === $contextProperty) {
				throw new JsonErrorCustomPropertyNameException();
			}

			$this->set($contextProperty, $context);
		}

		$this->hasError = true;
	}

	/**
	 * Ensure that the document object is a JsonKvpObject.
	 *
	 * @throws JsonTypeException If the document object is not a JsonKvpObject
	 */
	private function ensureJsonKvpObject():void {
		if(!$this->jsonObject) {
			$this->jsonObject = new JsonKvpObject();
		}

		if(!$this->jsonObject instanceof JsonKvpObject) {
			throw new JsonTypeException("Internal document object is already set as not a " . JsonKvpObject::class);
		}
	}

	/**
	 * Set a simple key-value pair in the document.
	 *
	 * @param string $key The key to set
	 * @param mixed $value The value to set
	 */
	private function setSimpleKey(string $key, mixed $value):void {
		if($this->jsonObject) {
			$this->jsonObject = $this->jsonObject->with($key, $value);
		}
	}

	/**
	 * Set a multi-part key in the document.
	 *
	 * @param array<int, string> $keyParts The key parts
	 * @param mixed $value The value to set
	 */
	private function setNestedKey(string $key, mixed $value):void {
		$keyParts = explode(".", $key);
		if(!$this->jsonObject) {
			return;
		}

		$currentKey = array_shift($keyParts);
		if($currentKey === null) {
			return;
		}

		$remainingKey = implode(".", $keyParts);

		// Get or create the current level object
		$currentObject = $this->jsonObject->contains($currentKey) &&
		$this->jsonObject->get($currentKey) instanceof JsonKvpObject
			? $this->jsonObject->get($currentKey)
			: new JsonKvpObject();

		// Create a temporary document to handle the remaining key parts
		$tempDoc = new JsonDocument($currentObject);
		$tempDoc->set($remainingKey, $value);

		// Update the root object
		$this->jsonObject = $this->jsonObject->with($currentKey, $tempDoc->jsonObject);
	}

	/**
	 * Get a value from the document using dot notation for nested objects.
	 *
	 * @param string $key The key to get, can use dot notation for nested objects
	 * @return null|bool|int|float|string|JsonObject|JsonDocument The value at the specified key
	 */
	public function get(string $key):null|bool|int|float|string|JsonObject|JsonDocument {
		if(!isset($this->jsonObject)) {
			return null;
		}

		if(!str_contains($key, ".")) {
			return $this->getSimpleKey($key);
		}

		return $this->getNestedKey($key);
	}

	/**
	 * Get a value from a simple key in the document.
	 *
	 * @param string $key The key to get
	 * @return null|bool|int|float|string|JsonObject|JsonDocument The value at the specified key
	 */
	private function getSimpleKey(string $key):null|bool|int|float|string|JsonObject|JsonDocument {
		if(!$this->jsonObject) {
			return null;
		}

		$value = $this->jsonObject->get($key);

		if($value instanceof JsonObject) {
			// Wrap JsonObject in a JsonDocument to support nested dot notation
			return new JsonDocument($value);
		}

		return $this->formatReturnValue($value);
	}

	/**
	 * Get a value from a nested key in the document using dot notation.
	 *
	 * @param string $key The key to get, using dot notation
	 * @return null|bool|int|float|string|JsonObject The value at the specified key
	 */
	private function getNestedKey(string $key):null|bool|int|float|string|JsonObject {
		if(!$this->jsonObject) {
			return null;
		}

		$keyParts = explode(".", $key);
		return $this->traverseKeyParts($keyParts, $this->jsonObject);
	}

	/**
	 * Traverse the key parts to find the value at the specified path.
	 *
	 * @param array<int, string> $keyParts The key parts to traverse
	 * @param JsonObject $currentObject The current object being traversed
	 * @return null|bool|int|float|string|JsonObject The value at the specified path
	 */
	private function traverseKeyParts(array $keyParts, JsonObject $currentObject):null|bool|int|float|string|JsonObject {
		foreach($keyParts as $i => $part) {
			if(!$currentObject instanceof JsonKvpObject || !$currentObject->contains($part)) {
				return null;
			}

			if($i === count($keyParts) - 1) {
				// Last part, get the value
				$value = $currentObject->get($part);
				return $this->formatReturnValue($value);
			}

			$currentObject = $currentObject->get($part);
		}

		// This should never be reached, but just in case
		return null;
	}

	/**
	 * Format the return value to ensure it matches the expected return type.
	 *
	 * @param mixed $value The value to format
	 * @return null|bool|int|float|string|JsonObject The formatted value
	 */
	private function formatReturnValue(mixed $value):null|bool|int|float|string|JsonObject {
		if(is_null($value)) {
			return null;
		}

		if(is_scalar($value)) {
			return $value;
		}

		if($value instanceof JsonObject) {
			return $value;
		}

		return null;
	}
}
