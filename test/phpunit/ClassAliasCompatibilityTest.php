<?php
namespace GT\Json\Test;

use PHPUnit\Framework\TestCase;

class ClassAliasCompatibilityTest extends TestCase {
	public function testLegacyGtClassNamesResolveToNewApi():void {
		self::assertTrue(class_exists(\GT\Json\JsonObjectBuilder::class));
		self::assertTrue(class_exists(\GT\Json\Schema\JsonDocument::class));
		self::assertTrue(class_exists(\GT\Json\JsonPrimitive\JsonStringPrimitive::class));

		$builder = new \GT\Json\JsonObjectBuilder();
		$json = $builder->fromAssociativeArray(["name" => "Ada"]);

		self::assertInstanceOf(\GT\Json\JSONObjectBuilder::class, $builder);
		self::assertInstanceOf(\GT\Json\JSONObject::class, $json);
		self::assertSame("Ada", $json->getString("name"));
	}

	public function testLegacyGtLowercaseNamespaceAliasesAlsoResolve():void {
		self::assertTrue(class_exists(\Gt\Json\JsonObjectBuilder::class));
		self::assertTrue(class_exists(\Gt\Json\Schema\JsonDocument::class));

		$builder = new \Gt\Json\JsonObjectBuilder();
		$document = new \Gt\Json\Schema\JsonDocument();
		$document->set("name", "Ada");

		self::assertInstanceOf(\GT\Json\JSONObjectBuilder::class, $builder);
		self::assertInstanceOf(\GT\Json\Schema\JSONDocument::class, $document);
		self::assertSame("Ada", $document->get("name"));
	}
}
