<?php
namespace Gt\Json\Test\Schema;

use Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonPrimitive\JsonStringPrimitive;
use Gt\Json\Schema\ValidationError;
use Gt\Json\Schema\ValidationSuccess;
use Gt\Json\Schema\Validator;
use PHPUnit\Framework\TestCase;
use Throwable;

class ValidatorTest extends TestCase {
	public function testValidate_noRules():void {
		$sut = new Validator();
		$jsonTest = (new JsonStringPrimitive())->withPrimitiveValue("test");
		$result = $sut->validate($jsonTest);
		self::assertInstanceOf(ValidationSuccess::class, $result);
	}

	public function testValidate_simple():void {
		$builder = new JsonObjectBuilder();
		$jsonSchema = $builder->fromJsonString(file_get_contents(__DIR__ . "/ExampleSchema/01-simple-min-length.json"));
		$sut = new Validator($jsonSchema);
		$jsonTest = (new JsonStringPrimitive())->withPrimitiveValue("test123");

		$result = $sut->validate($jsonTest);
		self::assertInstanceOf(ValidationSuccess::class, $result);
	}

	public function testValidate_simpleFail():void {
		$builder = new JsonObjectBuilder();
		$jsonSchema = $builder->fromJsonString(file_get_contents(__DIR__ . "/ExampleSchema/01-simple-min-length.json"));
		$sut = new Validator($jsonSchema);
		$jsonTest = (new JsonStringPrimitive())->withPrimitiveValue("test"); // this is too short (5 characters min)!

		$result = $sut->validate($jsonTest);
		self::assertInstanceOf(ValidationError::class, $result);
		$errorList = $result->getErrorList();
		self::assertContains("Minimum string length is 5, found 4", $errorList);
		self::assertCount(1, $errorList);
	}

	public function testValidate_object():void {
		$builder = new JsonObjectBuilder();
		$jsonSchema = $builder->fromJsonString(file_get_contents(__DIR__ . "/ExampleSchema/02-object.json"));
		$sut = new Validator($jsonSchema);
		$jsonString = <<<JSON
		{
			"name": "Cody",
			"colour": "orange",
			"food": ["biscuits", "mushroom", "corn on the cob"]
		}
		JSON;

		$jsonTest = $builder->fromJsonString($jsonString);

		$result = $sut->validate($jsonTest);
		self::assertInstanceOf(ValidationSuccess::class, $result);
	}

	public function testValidate_objectMultipleErrors():void {
		$builder = new JsonObjectBuilder();
		$jsonSchema = $builder->fromJsonString(file_get_contents(__DIR__ . "/ExampleSchema/02-object.json"));
		$sut = new Validator($jsonSchema);
		$jsonString = <<<JSON
		{
			"name": "",
			"colour": 105,
			"food": ["biscuits", "mushroom", "corn on the cob", true, 12345],
			"toy": "mouse"
		}
		JSON;

		$jsonTest = $builder->fromJsonString($jsonString);

		$result = $sut->validate($jsonTest);
		self::assertInstanceOf(ValidationError::class, $result);

		// TODO: How should the error object present the multiple errors, referencing the fields that are at fault?
	}
}
