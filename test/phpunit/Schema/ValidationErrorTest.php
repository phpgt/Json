<?php
namespace Gt\Json\Test\Schema;

use Gt\Json\JsonKvpObject;
use Gt\Json\Schema\ValidationError;
use PHPUnit\Framework\TestCase;

class ValidationErrorTest extends TestCase {
	public function testGetErrorList():void {
		$expectedErrorList = [
			"/" => "This is an example error on the root object",
			"/something" => "This is an example error on the something property",
		];

		$schema = self::createMock(JsonKvpObject::class);
		$sut = new ValidationError($schema, $expectedErrorList);
		self::assertSame($expectedErrorList, $sut->getErrorList());
	}

	public function testJsonSerialize():void {
		$expectedErrorList = [
			"/" => "This is an example error on the root object",
			"/something" => "This is an example error on the something property",
		];

		$expectedSchemaData = [
			"\$schema" => "https://json-schema.org/draft/2020-12/schema",
			"title" => "Example schema",
			"type" => "object",
			"additionalProperties" => false,
		];
		$schema = self::createMock(JsonKvpObject::class);
		$schema->expects(self::once())
			->method("jsonSerialize")
			->willReturn($expectedSchemaData);
		$sut = new ValidationError($schema, $expectedErrorList);
		$jsonString = json_encode($sut);
		self::assertSame('{"error":"The provided object does not match the schema","errorList":' . json_encode($expectedErrorList) . ',"schema":' . json_encode($expectedSchemaData) . '}', $jsonString);
	}
}
