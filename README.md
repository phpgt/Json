Structured, type-safe, immutable JSON objects. 
==============================================

Built on top of [PHP.Gt/DataObject][dataobject], this repository adds JSON-specific compatibility. The main usage will be the `JsonObjectBuilder` class that can be used to build a type of `JsonObject` from a JSON string or decoded JSON object (from `json_decode`).

The purpose of using these classes to represent decoded JSON data is to provide a type-safe, immutable interface to the enclosed data.

***

<a href="https://giub.com/PhpGt/Json/actions" target="_blank">
	<img src="https://badge.status.php.gt/json-build.svg" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Json" target="_blank">
	<img src="https://badge.status.php.gt/json-quality.svg" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Json" target="_blank">
	<img src="https://badge.status.php.gt/json-coverage.svg" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/Json" target="_blank">
	<img src="https://badge.status.php.gt/json-version.svg" alt="Current version" />
</a>
<a href="http://www.php.gt/json" target="_blank">
	<img src="https://badge.status.php.gt/json-docs.svg" alt="PHP.Gt/Json documentation" />
</a>

The abstract `JsonObject` class extends the [`DataObject` base class][dataobject] to represent the root element of a JSON object. In JSON, this may not necessarily be a key-value-pair object.

The following JSON strings can all be successfully decoded:

+ `{"type": "key-value-pair"}` - a typical key-value-pair object
+ `[{"name": "first"}, {"name": "second"}` - an array of objects
+ `0` - an integer
+ `1.05` - a floating point
+ `false` - a boolean
+ `"Today is going to be a good day"` - a string
+ `null` - a null

Because of this, the base `DataObject` would be unable to represent the different types of scalar value in a type-safe way. The `JsonObjectBuilder` class returns a new instance of the abstract `JsonObject` class which is one of the following types:

+ `JsonKvpObject` - identical features to `DataObject` with type-safe getters for its keys
+ `JsonPrimitive` - a representation of the primitive value, further broken down into types `JsonArrayPrimitive`, `JsonBoolPrimitive`, `JsonFloatPrimitive`, `JsonIntPrimitive`, `JsonNullPrimitive` and `JsonStringPrimitive`.

Usage example
-------------

```php
use \Gt\Json\JsonObjectBuilder;
use Gt\Json\JsonKvpObject;
use \Gt\Json\JsonPrimitive\JsonPrimitive;

$response = file_get_contents("https://example.com/details.json");
$builder = new JsonObjectBuilder();
$jsonObject = $builder->fromJsonString($response);

if($jsonObject instanceof JsonKvpObject) {
	$id = $jsonObject->getInt("id");
}
elseif($jsonObject instanceof JsonPrimitive) {
	$id = $jsonObject->getPrimitiveValue();
}

echo "Requested ID is: $id";
```

Fetch API
---------

Check out the [PHP implementation of the Fetch API][fetch] that uses this library to work with JSON endpoints asynchronously.

[dataobject]: https://www.php.gt/dataobject
[fetch]: https://www.php.gt/fetch