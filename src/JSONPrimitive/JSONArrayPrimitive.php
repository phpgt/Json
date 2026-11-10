<?php
namespace GT\Json\JSONPrimitive;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, mixed> */
class JSONArrayPrimitive extends JSONPrimitive implements Countable, IteratorAggregate {
	public function __construct() {
		parent::__construct();
		$this->value = [];
	}

	/** @return array<int, mixed> */
	public function getPrimitiveValue():array {
		return (array)$this->value;
	}

	public function count():int {
		return count($this->getPrimitiveValue());
	}

	/** @return Traversable<int, mixed> */
	public function getIterator():Traversable {
		return new ArrayIterator($this->getPrimitiveValue());
	}
}
