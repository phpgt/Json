<?php
namespace Gt\Json\Test;

use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;
use PHPUnit\Framework\TestCase;

class JsonObjectBuilderTest extends TestCase {
	private string $jsonStringSimpleKVP = <<<JSON
		{
			"id": 123,
			"name": "Example"
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
		/** @var JsonNullPrimitive $jsonObject */
		$jsonObject = $sut->fromJsonString($this->jsonStringNull);
		self::assertInstanceOf(JsonNullPrimitive::class, $jsonObject);
		self::assertNull($jsonObject->getPrimitiveValue());
	}
}