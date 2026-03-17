<?php
namespace GT\Json\Test\Schema;

use GT\Json\JSONErrorCustomPropertyNameException;
use GT\Json\JSONErrorStateException;
use GT\Json\JSONObjectBuilder;
use GT\Json\JSONPrimitive\JSONArrayPrimitive;
use GT\Json\JSONPrimitive\JSONStringPrimitive;
use GT\Json\Schema\JSONDocument;
use PHPUnit\Framework\TestCase;

class JSONDocumentTest extends TestCase {
	public function testToString_empty():void {
// An empty response should be zero bytes, because `null` is still a datum that
// represents some kind of response. If there is no data, `null` is not
// accurate.
		$sut = new JSONDocument();
		self::assertSame("", (string)$sut);
	}

	public function testToString_array():void {
		$testArray = ["One", "Two", "Three"];

		$jsonArrayPrimitive = new JSONArrayPrimitive();
		$jsonArrayPrimitive = $jsonArrayPrimitive->withPrimitiveValue($testArray);
		$sut = new JSONDocument($jsonArrayPrimitive);
		self::assertSame(json_encode($testArray), (string)$sut);
	}

	public function testSetObject_stringPrimitive():void {
		$jsonObject = (new JSONStringPrimitive())->withPrimitiveValue("test");
		$sut = new JSONDocument();
		$sut->setObject($jsonObject);
		self::assertSame("\"test\"", (string)$sut);
	}

	public function testSetObject_complexObject():void {
		$builder = new JSONObjectBuilder();
		$jsonObject = $builder->fromAssociativeArray([
			"name" => "John Carmack",
			"releases" => ["Shadowforge", "Catacomb", "Commander Keen", "Wolfenstein", "Doom", "Quake"],
		]);
		$sut = new JSONDocument();
		$sut->setObject($jsonObject);

		self::assertSame(
			'{"name":"John Carmack","releases":["Shadowforge","Catacomb","Commander Keen","Wolfenstein","Doom","Quake"]}',
			(string)$sut
		);
	}

	public function testGet_notSet():void {
		$sut = new JSONDocument();
		self::assertNull($sut->get("nothing"));
	}

	public function testSet():void {
		$sut = new JSONDocument();
		$name = "John Carmack";
		$sut->set("name", $name);
		self::assertSame($name, (string)$sut->get("name"));
	}

	public function testSet_nested():void {
		$sut = new JSONDocument();
		$sut->set("department.name", "Computer Science");
		self::assertSame("Computer Science", $sut->get("department.name"));
	}

	public function testSet_nestedGet():void {
		$sut = new JSONDocument();
		$sut->set("department.name", "Computer Science");
		$department = $sut->get("department");
		self::assertSame("Computer Science", $department->get("name"));
	}

	public function testSet_veryNested():void {
		$sut = new JSONDocument();
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
		$builder = new JSONObjectBuilder();
		$jsonObject = $builder->fromJsonDecoded($exampleArray);
		$sut = new JSONDocument($jsonObject);
		self::assertSame(json_encode($exampleArray), (string)$sut);
	}

	public function testError(): void {
		$sut = new JSONDocument();
		$sut->error("This is an error");

		self::assertSame(json_encode(["error" => "This is an error"]), (string)$sut);
	}

	public function testError_overridesCurrentProperties():void {
		$sut = new JSONDocument();
		$sut->set("one", "example");
		$sut->error("This is an error");

		self::assertSame(json_encode(["error" => "This is an error"]), (string)$sut);
	}

	public function testError_context():void {
		$sut = new JSONDocument();
		$context = [
			"one" => "example",
			"two" => "example",
			"three" => "example",
		];

		$sut->error("This is an error", context: $context);

		self::assertSame(json_encode([
			"error" => "This is an error",
			"errorContext" => $context,
		]), (string)$sut);
	}

	public function testError_contextCustomPropertyName(): void {
		$sut = new JSONDocument();
		$context = [
			"one" => "example",
			"two" => "example",
			"three" => "example",
		];

		$sut->error("This is an error", context: $context, contextProperty: "data");

		self::assertSame(json_encode([
			"error" => "This is an error",
			"data" => $context,
		]), (string)$sut);
	}

	public function testError_customPropertyNamesMustBeDifferent(): void {
		$sut = new JSONDocument();
		$context = [
			"one" => "example",
			"two" => "example",
			"three" => "example",
		];

		self::expectException(JSONErrorCustomPropertyNameException::class);

		$sut->error("This is an error", context: $context, property: "data", contextProperty: "data");
	}

	public function testError_disallowsSetAfterError():void {
		$sut = new JSONDocument();

		self::expectException(JSONErrorStateException::class);

		$sut->error("This is an error");
		$sut->set("one", "example");
	}

	public function testSetErrorCallback():void {
		$calls = [];
		$callback = function(string $message, ?array $context = null)use(&$calls):void {
			array_push($calls, [$message, $context]);
		};

		$sut = new JSONDocument();
		$sut->setErrorCallback($callback);

		$sut->error("Test");
		self::assertCount(1, $calls);
		self::assertSame("Test", $calls[0][0]);
		self::assertNull($calls[0][1]);
	}

	public function testSetErrorCallback_context():void {
		$calls = [];
		$callback = function(string $message, ?array $context = null)use(&$calls):void {
			array_push($calls, [$message, $context]);
		};

		$sut = new JSONDocument();
		$sut->setErrorCallback($callback);

		$sut->error("Test", ["example" => "message"]);
		self::assertCount(1, $calls);
		self::assertSame(["example" => "message"], $calls[0][1]);
	}
}
