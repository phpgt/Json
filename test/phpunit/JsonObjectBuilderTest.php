<?php
namespace Gt\Json\Test;

use Gt\Json\JsonDecodeException;
use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonArrayPrimitive;
use Gt\Json\JsonPrimitive\JsonBoolPrimitive;
use Gt\Json\JsonPrimitive\JsonFloatPrimitive;
use Gt\Json\JsonPrimitive\JsonIntPrimitive;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use PHPUnit\Framework\TestCase;
use stdClass;

class JsonObjectBuilderTest extends TestCase {
	private string $jsonStringSimpleKVP = <<<JSON
		{
			"id": 123,
			"name": "Example"
		}
		JSON;
	private string $jsonStringContainingArray = <<<JSON
		{
			"id": 123,
			"name": "Example",
			"tags": ["test", "data", "json", "classic"]
		}
		JSON;
	private string $jsonStringNull = <<<JSON
		null
		JSON;
	private string $jsonStringBool = <<<JSON
		true
		JSON;
	private string $jsonStringInt = <<<JSON
		123
		JSON;
	private string $jsonStringFloat = <<<JSON
		123.456
		JSON;
	private string $jsonStringString = <<<JSON
		"Example!"
		JSON;
	private string $jsonStringArray = <<<JSON
		["one", "two", "three"]
		JSON;
	private string $jsonStringArrayContainingSimpleKVP = <<<JSON
		[
			"one",
			"two",
			{
				"id": 123,
				"name": "Example"
			}
		]
		JSON;


	public function testFromJsonString() {
		$sut = new JsonObjectBuilder();
		$jsonObject = $sut->fromJsonString($this->jsonStringSimpleKVP);
		self::assertInstanceOf(JsonKvpObject::class, $jsonObject);
		self::assertEquals(123, $jsonObject->getInt("id"));
		self::assertEquals("Example", $jsonObject->getString("name"));
	}

	public function testFromJsonDecoded() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringSimpleKVP);
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonKvpObject::class, $jsonObject);
		self::assertEquals(123, $jsonObject->getInt("id"));
		self::assertEquals("Example", $jsonObject->getString("name"));
	}

	public function testFromJsonDecodedAsArray() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringSimpleKVP, true);
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonKvpObject::class, $jsonObject);
		self::assertEquals(123, $jsonObject->getInt("id"));
		self::assertEquals("Example", $jsonObject->getString("name"));
	}

	public function testFromJsonDecodedNull() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringNull);
		/** @var JsonNullPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonNullPrimitive::class, $jsonObject);
		self::assertNull($jsonObject->getPrimitiveValue());
	}

	public function testFromJsonDecodedBool() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringBool);
		/** @var JsonBoolPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonBoolPrimitive::class, $jsonObject);
		self::assertTrue($jsonObject->getPrimitiveValue());
	}

	public function testFromJsonDecodedInt() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringInt);
		/** @var JsonIntPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonIntPrimitive::class, $jsonObject);
		self::assertSame(123, $jsonObject->getPrimitiveValue());
	}

	public function testFromJsonDecodedFloat() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringFloat);
		/** @var JsonFloatPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonFloatPrimitive::class, $jsonObject);
		self::assertSame(123.456, $jsonObject->getPrimitiveValue());
	}

	public function testFromJsonDecodedString() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringString);
		/** @var JsonStringPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonStringPrimitive::class, $jsonObject);
		self::assertSame("Example!", $jsonObject->getPrimitiveValue());
	}

	public function testFromJsonDecodedArray() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringArray);
		/** @var JsonArrayPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertInstanceOf(JsonArrayPrimitive::class, $jsonObject);
		self::assertSame(["one", "two", "three"], $jsonObject->getPrimitiveValue());
	}

	public function testFromJsonStringContainingArray() {
		$sut = new JsonObjectBuilder();
		$jsonObject = $sut->fromJsonString($this->jsonStringContainingArray);
		self::assertEquals(123, $jsonObject->getInt("id"));
		self::assertEquals("Example", $jsonObject->getString("name"));
		$array = $jsonObject->getArray("tags");
		self::assertCount(4, $array);
		self::assertEquals("test", $array[0]);
		self::assertEquals("data", $array[1]);
		self::assertEquals("json", $array[2]);
		self::assertEquals("classic", $array[3]);
	}

	public function testFromJsonDecodedContainingArrayWhenDecodedAsArray() {
		$sut = new JsonObjectBuilder();
		$json = json_decode($this->jsonStringContainingArray, true);
		$jsonObject = $sut->fromJsonDecoded($json);
		self::assertEquals(123, $jsonObject->getInt("id"));
		self::assertEquals("Example", $jsonObject->getString("name"));
		$array = $jsonObject->getArray("tags");
		self::assertCount(4, $array);
		self::assertEquals("test", $array[0]);
		self::assertEquals("data", $array[1]);
		self::assertEquals("json", $array[2]);
		self::assertEquals("classic", $array[3]);
	}

	public function testFromJsonStringArrayContainingSimpleKVP() {
		$sut = new JsonObjectBuilder();
		/** @var JsonArrayPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonString($this->jsonStringArrayContainingSimpleKVP);
		self::assertInstanceOf(JsonArrayPrimitive::class, $jsonObject);
		$array = $jsonObject->getPrimitiveValue();
		self::assertCount(3, $array);
		self::assertEquals("one", $array[0]);
		self::assertEquals("two", $array[1]);
		$object = $array[2];
		self::assertInstanceOf(JsonKvpObject::class, $object);
		self::assertEquals(123, $object->getInt("id"));
		self::assertEquals("Example", $object->getString("name"));
	}

	public function testFromJson_syntaxError() {
		$jsonString = '{ "name": "Greg", "title: "Clumsy programmer!" }';
		$sut = new JsonObjectBuilder();
		self::expectException(JsonDecodeException::class);
		self::expectExceptionMessage("Error decoding JSON: Syntax error");
		$sut->fromJsonString($jsonString);
	}

	public function testFromJson_illegalCharacters() {
		$jsonString = "{ \"name\": \"Greg\", \"favourite_characters\": \"\xB1\x31\" }";
		$sut = new JsonObjectBuilder();
		self::expectException(JsonDecodeException::class);
		self::expectExceptionMessage("Error decoding JSON: Malformed UTF-8 characters, possibly incorrectly encoded");
		$sut->fromJsonString($jsonString);
	}

	public function testFromJson_depth() {
		$jsonString = <<<JSON
		{
			"name": "Greg",
			"address": {
				"home": {
					"addressId": 105
				},
				"work": {
					"addressId": 210
				}
			}
		}
		JSON;

		$sut = new JsonObjectBuilder(3);
		self::expectException(JsonDecodeException::class);
		self::expectExceptionMessage("Error decoding JSON: Maximum stack depth exceeded");
		$sut->fromJsonString($jsonString);
	}
}
