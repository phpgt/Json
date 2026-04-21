<?php
namespace GT\Json;

use Throwable;

class JSONDecodeException extends JSONException {
	public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
		parent::__construct("Error decoding JSON: $message", $code, $previous);
	}
}
