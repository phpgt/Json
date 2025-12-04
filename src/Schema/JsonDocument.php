<?php
namespace Gt\Json\Schema;

use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObject;
use Gt\Json\JsonTypeException;

class JsonDocument {
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

	public function set(string $key, mixed $value):void {
		if(!$this->jsonObject) {
			$this->jsonObject = new JsonKvpObject();
		}

		if(!$this->jsonObject instanceof JsonKvpObject) {
			throw new JsonTypeException("Internal document object is already set as not a " . JsonKvpObject::class);
		}

		if(str_contains($key, ".")) {
			$keyParts = explode(".", $key);

			// For simple two-part keys like "department.name"
			if(count($keyParts) === 2) {
				$parentKey = $keyParts[0];
				$childKey = $keyParts[1];

				// Get or create the parent object
				$parentObject = $this->jsonObject->contains($parentKey) && 
					$this->jsonObject->get($parentKey) instanceof JsonKvpObject
					? $this->jsonObject->get($parentKey)
					: new JsonKvpObject();

				// Set the child value
				$parentObject = $parentObject->with($childKey, $value);

				// Update the root object
				$this->jsonObject = $this->jsonObject->with($parentKey, $parentObject);
				return;
			}

			// For more complex nested keys
			$currentKey = array_shift($keyParts);
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
			return;
		}

		$this->jsonObject = $this->jsonObject->with($key, $value);
	}

	public function get(string $key):null|bool|int|float|string|JsonObject|JsonDocument {
		if(!isset($this->jsonObject)) {
			return null;
		}

		if(!str_contains($key, ".")) {
			// Simple key, just get it directly
			$value = $this->jsonObject->get($key);

			// For the test case where we need to return the raw value
			if($value instanceof JsonObject) {
				// Wrap JsonObject in a JsonDocument to support nested dot notation
				return new JsonDocument($value);
			}

			return $this->formatReturnValue($value);
		}

		$keyParts = explode(".", $key);
		$currentObject = $this->jsonObject;

		foreach($keyParts as $i => $part) {
			if(!$currentObject instanceof JsonKvpObject) {
				return null;
			}

			if(!$currentObject->contains($part)) {
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

	private function formatReturnValue(mixed $value):null|bool|int|float|string|JsonObject {
		if(is_null($value)) {
			return null;
		}
		elseif(is_scalar($value)) {
			return $value;
		}
		elseif(is_array($value)) {
			return $value;
		}

		return $value;
	}
}
