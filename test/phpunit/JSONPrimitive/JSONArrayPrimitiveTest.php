<?php
namespace GT\Json\Test\JSONPrimitive;

use GT\Json\JSONObjectBuilder;
use GT\Json\JSONPrimitive\JSONArrayPrimitive;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JSONArrayPrimitiveTest extends TestCase {
	#[DataProvider("arrayValues")]
	public function testCount_countsTopLevelItems(array $values, int $expected):void {
		$sut = (new JSONArrayPrimitive())->withPrimitiveValue($values);

		self::assertSame($expected, count($sut));
	}

	public static function arrayValues():iterable {
		yield "empty" => [[], 0];
		yield "null and false are items" => [[null, false, 0, ""], 4];
		yield "nested arrays count once" => [[[1, 2], [3]], 2];
	}

	public function testConstruct_emptyArrayCanBeCountedAndIterated():void {
		$sut = new JSONArrayPrimitive();

		self::assertSame(0, count($sut));
		self::assertSame([], iterator_to_array($sut));
		self::assertSame("[]", (string)$sut);
	}

	public function testGetIterator_preservesKeysAndNullValues():void {
		$sut = (new JSONArrayPrimitive())->withPrimitiveValue([2 => "first", 5 => null, 9 => false]);

		self::assertSame([2 => "first", 5 => null, 9 => false], iterator_to_array($sut));
		self::assertSame([2 => "first", 5 => null, 9 => false], iterator_to_array($sut));
		self::assertSame(3, count($sut));
	}

	public function testGetIterator_nestedLoopsAreIndependent():void {
		$sut = (new JSONArrayPrimitive())->withPrimitiveValue(["a", "b"]);
		$pairs = [];

		foreach($sut as $outer) {
			foreach($sut as $inner) {
				$pairs[] = $outer . $inner;
			}
		}

		self::assertSame(["aa", "ab", "ba", "bb"], $pairs);
	}

	public function testGetIterator_supportsObjectsFromDecodedJson():void {
		$sut = (new JSONObjectBuilder())->fromJsonString('[{"name":"Ada"},{"name":"Grace"}]');
		$names = [];

		foreach($sut as $key => $person) {
			$names[$key] = $person->getString("name");
		}

		self::assertSame(["Ada", "Grace"], $names);
		self::assertSame(2, count($sut));
		self::assertSame('[{"name":"Ada"},{"name":"Grace"}]', (string)$sut);
	}

	public function testWithPrimitiveValue_doesNotChangeAnExistingIterator():void {
		$sut = (new JSONArrayPrimitive())->withPrimitiveValue(["original"]);
		$iterator = $sut->getIterator();

		$updated = $sut->withPrimitiveValue(["replacement", "extra"]);

		self::assertSame(["original"], iterator_to_array($iterator));
		self::assertSame(["original"], iterator_to_array($sut));
		self::assertSame(["replacement", "extra"], iterator_to_array($updated));
		self::assertSame(2, count($updated));
	}
}
