<?php
namespace GT\Json\Schema;

use GT\Json\JSONErrorCustomPropertyNameException;
use GT\Json\JSONErrorStateException;
use GT\Json\JSONKvpObject;
use GT\Json\JSONObject;
use GT\Json\JSONTypeException;

class JSONDocument {
	private bool $hasError = false;
	/** @var null|callable(string, null|array<string, mixed>):void */
	private $errorCallback = null;

	public function __construct(
		private ?JSONObject $jsonObject = null,
	) {}

	public function __toString():string {
		if(is_null($this->jsonObject)) {
			return "";
		}

		return json_encode($this->jsonObject) ?: "";
	}

	public function setObject(JSONObject $jsonObject):void {
		$this->jsonObject = $jsonObject;
	}

	/**
	 * Set a value in the document using dot notation for nested objects.
	 *
	 * @param string $key The key to set, can use dot notation for nested objects
	 * @param mixed $value The value to set
	 * @throws JSONTypeException If the document object is not a JSONKvpObject
	 */
	public function set(string $key, mixed $value):void {
		if ($this->hasError) {
			throw new JSONErrorStateException();
		}

		$this->ensureJSONKvpObject();

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
	 * @param null|array<string, mixed> $context An optional array containing error context to be returned
	 * @param string $property Optional property name for the error message
	 * @param string $contextProperty Optional property name for the error context array
	 *
	 * @throws JSONErrorCustomPropertyNameException If clashing property names are provided
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
				throw new JSONErrorCustomPropertyNameException();
			}

			$this->set($contextProperty, $context);
		}

		$this->hasError = true;
		if($this->errorCallback !== null) {
			call_user_func($this->errorCallback, $message, $context);
		}
	}

	public function setErrorCallback(callable $callback):void {
		$this->errorCallback = $callback;
	}

	/**
	 * Ensure that the document object is a JSONKvpObject.
	 *
	 * @throws JSONTypeException If the document object is not a JSONKvpObject
	 */
	private function ensureJSONKvpObject():void {
		if(!$this->jsonObject) {
			$this->jsonObject = new JSONKvpObject();
		}

		if(!$this->jsonObject instanceof JSONKvpObject) {
			throw new JSONTypeException("Internal document object is already set as not a " . JSONKvpObject::class);
		}
	}

	private function setSimpleKey(string $key, mixed $value):void {
		if($this->jsonObject) {
			$this->jsonObject = $this->jsonObject->with($key, $value);
		}
	}

	private function setNestedKey(string $key, mixed $value):void {
		$keyParts = explode(".", $key);
		if(!$this->jsonObject) {
			return;
		}

		/** @var null|string $currentKey */
		$currentKey = array_shift($keyParts);
		if(!$currentKey) {
			return;
		}

		$remainingKey = implode(".", $keyParts);

		// Get or create the current level object
		$currentObject = $this->jsonObject->contains($currentKey) &&
		$this->jsonObject->get($currentKey) instanceof JSONKvpObject
			? $this->jsonObject->get($currentKey)
			: new JSONKvpObject();

		// Create a temporary document to handle the remaining key parts
		$tempDoc = new JSONDocument($currentObject);
		$tempDoc->set($remainingKey, $value);

		// Update the root object
		$this->jsonObject = $this->jsonObject->with($currentKey, $tempDoc->jsonObject);
	}

	/**
	 * Get a value from the document using dot notation for nested objects.
	 *
	 * @param string $key The key to get, can use dot notation for nested objects
	 * @return null|bool|int|float|string|JSONObject|JSONDocument The value at the specified key
	 */
	public function get(string $key):null|bool|int|float|string|JSONObject|JSONDocument {
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
	 * @return null|bool|int|float|string|JSONObject|JSONDocument The value at the specified key
	 */
	private function getSimpleKey(string $key):null|bool|int|float|string|JSONObject|JSONDocument {
		if(!$this->jsonObject) {
			return null;
		}

		$value = $this->jsonObject->get($key);

		if($value instanceof JSONObject) {
			// Wrap JSONObject in a JSONDocument to support nested dot notation
			return new JSONDocument($value);
		}

		return $this->formatReturnValue($value);
	}

	/**
	 * Get a value from a nested key in the document using dot notation.
	 *
	 * @param string $key The key to get, using dot notation
	 * @return null|bool|int|float|string|JSONObject The value at the specified key
	 */
	private function getNestedKey(string $key):null|bool|int|float|string|JSONObject {
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
	 * @param JSONObject $currentObject The current object being traversed
	 * @return null|bool|int|float|string|JSONObject The value at the specified path
	 */
	private function traverseKeyParts(array $keyParts, JSONObject $currentObject):null|bool|int|float|string|JSONObject {
		foreach($keyParts as $i => $part) {
			if(!$currentObject instanceof JSONKvpObject || !$currentObject->contains($part)) {
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
	 * @return null|bool|int|float|string|JSONObject The formatted value
	 */
	private function formatReturnValue(mixed $value):null|bool|int|float|string|JSONObject {
		if(is_null($value)) {
			return null;
		}

		if(is_scalar($value)) {
			return $value;
		}

		if($value instanceof JSONObject) {
			return $value;
		}

		return null;
	}
}
