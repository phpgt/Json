<?php
namespace Gt\Json;

use Throwable;

class JsonDecodeException extends JsonException {
	public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
		parent::__construct("Error decoding JSON: $message", $code, $previous);
	}
}
