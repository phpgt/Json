<?php
namespace Gt\Json;

use Iterator;

class JsonKvpObject extends JsonObject implements Iterator {
	public function rewind():void {
		reset($this->data);
	}

	public function current():mixed {
		return current($this->data);
	}

	public function next():void {
		next($this->data);
	}

	public function key():mixed {
		return key($this->data);
	}

	public function valid():bool {
		return isset($this->data[$this->key()]);
	}
}
