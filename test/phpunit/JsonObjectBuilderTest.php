<?php
namespace Gt\Json\Test;

use Gt\Json\JsonKvpObject;
use Gt\Json\JsonObjectBuilder;
use PHPUnit\Framework\TestCase;

class JsonObjectBuilderTest extends TestCase {
	private string $jsonStringSimpleKVP = <<<JSON
		{
			"id": 123,
			"name": "Example"
		}
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
}