<?php
namespace Gt\Json\Test\Schema;

use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonArrayPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use Gt\Json\Schema\JsonDocument;
use PHPUnit\Framework\TestCase;

class JsonDocumentTest extends TestCase {
	public function testToString_empty():void {
// An empty response should be zero bytes, because `null` is still a datum that
// represents some kind of response. If there is no data, `null` is not
// accurate.
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

	public function testSetObject_stringPrimitive():void {
		$jsonObject = (new JsonStringPrimitive())->withPrimitiveValue("test");
		$sut = new JsonDocument();
		$sut->setObject($jsonObject);
		self::assertSame("\"test\"", (string)$sut);
	}

	public function testSetObject_complexObject():void {
		$builder = new JsonObjectBuilder();
		$jsonObject = $builder->fromAssociativeArray([
			"name" => "John Carmack",
			"releases" => ["Shadowforge", "Catacomb", "Commander Keen", "Wolfenstein", "Doom", "Quake"],
		]);
		$sut = new JsonDocument();
		$sut->setObject($jsonObject);

		self::assertSame(
			'{"name":"John Carmack","releases":["Shadowforge","Catacomb","Commander Keen","Wolfenstein","Doom","Quake"]}',
			(string)$sut
		);
	}

	public function testGet_notSet():void {
		$sut = new JsonDocument();
		self::assertNull($sut->get("nothing"));
	}

	public function testSet():void {
		$sut = new JsonDocument();
		$name = "John Carmack";
		$sut->set("name", $name);
		self::assertSame($name, (string)$sut->get("name"));
	}

	public function testSet_nested():void {
		$sut = new JsonDocument();
		$sut->set("department.name", "Computer Science");
		self::assertSame("Computer Science", $sut->get("department.name"));
	}

	public function testSet_nesedGet():void {
		$sut = new JsonDocument();
		$sut->set("department.name", "Computer Science");
		$department = $sut->get("department");
		self::assertSame("Computer Science", $department->get("name"));
	}

	public function testSet_veryNested():void {
		$sut = new JsonDocument();
		$sut->set("one.two.three.four.five", "example");
		$one = $sut->get("one");
		$two = $one->get("two");
		$three = $two->get("three");
		self::assertSame("example", $three->get("four.five"));
	}

	public function testConstruct_jsonObject():void {
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
