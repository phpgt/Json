<?php
namespace Gt\Json\Test\Schema;

use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonArrayPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use Gt\Json\Schema\JsonDocument;
use PHPUnit\Framework\TestCase;

class JsonDocumentTest extends TestCase {
	public function testToString_empty():void {
		$sut = new JsonDocument();
		self::assertSame("", (string)$sut);
	}

	public function testToString_array():void {
		$testArray = ["One", "Two", "Three"];

		$jsonArrayPrimitive = new JsonArrayPrimitive();
		$jsonArrayPrimitive = $jsonArrayPrimitive->withPrimitiveValue($testArray);
		$sut = new JsonDocument($jsonArrayPrimitive);
		self::assertSame(json_encode($testArray), (string)$sut);
	}

	public function testSetJson_stringPrimitive():void {
		$jsonObject = (new JsonStringPrimitive())->withPrimitiveValue("test");
		$sut = new JsonDocument();
		$sut->setJson($jsonObject);
		self::assertSame("\"test\"", (string)$sut);
	}

	public function testSetJson_jsonObject():void {
		$exampleArray = [
			"name" => "Test",
			"age" => 99,
			"list" => ["One", "Two", "Three"]
		];
		$builder = new JsonObjectBuilder();
		$jsonObject = $builder->fromJsonDecoded($exampleArray);
		$sut = new JsonDocument($jsonObject);
		self::assertSame(json_encode($exampleArray), (string)$sut);
	}
}
