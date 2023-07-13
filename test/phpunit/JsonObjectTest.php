<?php
namespace Gt\Json\Test;
use Gt\Json\JsonKvpObject;
use Gt\Json\JsonPrimitive\JsonBoolPrimitive;
use Gt\Json\JsonPrimitive\JsonFloatPrimitive;
use Gt\Json\JsonPrimitive\JsonIntPrimitive;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use PHPUnit\Framework\TestCase;

class JsonObjectTest extends TestCase {
	public function testToString():void {
		$sut = (new JsonKvpObject())
			->with("key1", "value1")
			->with("key2", "value2");
		self::assertSame('{"key1":"value1","key2":"value2"}', (string)$sut);
	}

	public function testToString_scalarString():void {
		$sut = (new JsonStringPrimitive())
			->withPrimitiveValue("example");
		self::assertSame("\"example\"", (string)$sut);
	}

	public function testToString_scalarInt():void {
		$sut = (new JsonIntPrimitive())
			->withPrimitiveValue(105);
		self::assertSame("105", (string)$sut);
	}

	public function testToString_scalarFloat():void {
		$sut = (new JsonFloatPrimitive())
			->withPrimitiveValue(12.34);
		self::assertSame("12.34", (string)$sut);
	}

	public function testToString_scalarBool():void {
		$sut = (new JsonBoolPrimitive())
			->withPrimitiveValue(true);
		self::assertSame("true", (string)$sut);
	}

	public function testToString_scalarNull():void {
		$sut = (new JsonNullPrimitive());
		self::assertSame("null", (string)$sut);
	}

	public function testForeach():void {
		$sut = new JsonKvpObject();
		$i = 0;
		foreach($sut as $value) {
			$i++;
		}
		self::assertFalse(isset($value));
		self::assertSame(0, $i);
	}

	public function testForeach_iteratesOverKeyValue():void {
		$kvp = [
			"key1" => "value1",
			"key2" => "value2",
			"key3" => [1, 2, 3],
		];
		$sut = new JsonKvpObject();
		foreach($kvp as $key => $value) {
			$sut = $sut->with($key, $value);
		}

		$iteratedKeyList = [];
		$iteratedValueList = [];
		foreach($sut as $key => $value) {
			array_push($iteratedKeyList, $key);
			array_push($iteratedValueList, $value);
		}

		self::assertSame("key1", $iteratedKeyList[0]);
		self::assertSame("key2", $iteratedKeyList[1]);
		self::assertSame("key3", $iteratedKeyList[2]);

		self::assertSame("value1", $iteratedValueList[0]);
		self::assertSame("value2", $iteratedValueList[1]);
		self::assertSame([1, 2, 3], $iteratedValueList[2]);
	}
}
