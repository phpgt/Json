<?php
namespace Gt\Json\JsonPrimitive;

use Gt\Json\JsonObject;

abstract class JsonPrimitive extends JsonObject {
	abstract public function getPrimitiveValue();
}