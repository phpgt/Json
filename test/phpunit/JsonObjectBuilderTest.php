<?php
namespace Gt\Json\Test;

use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonArrayPrimitive;
use Gt\Json\JsonPrimitive\JsonBoolPrimitive;
use Gt\Json\JsonPrimitive\JsonFloatPrimitive;
use Gt\Json\JsonPrimitive\JsonIntPrimitive;
use Gt\Json\JsonPrimitive\JsonNullPrimitive;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
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
}