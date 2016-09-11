# Changelog

## 2.0.1

* **Fix** `~\Core\Route\Options::add()`, see [#1](https://github.com/inpsyde/WP-REST-Starter/issues/1).

## 2.0.0

* **[BREAKING]** **Finalize** all classes that implement at least one provided interface and that are not overcomplete.
* **[BREAKING]** **Remove** deprecated `~\Common\Endpoint\Handler` and `~\Common\Route\Options` interfaces, and `~\Core\Field\Definition` implementation.
* **[BREAKING]** **Remove** deprecated `to_array()` method from `~\Core\Field\Collection` and `~\Core\Route\Collection` implementations.
* **[BREAKING]** **Add** `get_title()` method to `~\Common\Endpoint\Schema` interface.
* **Adapt** `~\Core\Field\Field` and `~\Core\Route\Options` implementations.
* **Introduce** `~\Common\Endpoint\FieldProcessor` interface and `~\Core\Endpoint\FieldProcessor` implementation.
* **Introduce** `~\Common\Request\FieldProcessor` interface and `~\Core\Request\FieldProcessor` implementation.
* **Introduce** `~\Common\Response\DataAccess` interface and `~\Core\Response\LinkAwareDataAccess` implementation.
* **Introduce** `~\Common\Response\DataFilter` interface and `~\Core\Response\SchemaAwareDataFilter` implementation.
* **Add** optional `$object_type` parameter to `~\Common\Field\Reader` interface.
* **Add** optional `$request` and `$object_type` parameters to `~\Common\Field\Updater` interface.

## 1.1.0

* **Introduce** `~\Common\Endpoint\RequestHandler` interface.
* **Introduce** `~\Common\Field\ReadableField`, `~\Common\Field\UpdatableField` and `~\Common\Field\SchemaAwareField` interfaces.
* **Introduce** `~\Common\Route\ExtensibleOptions` and `~\Common\Route\SchemaAwareOptions` interfaces.
* **Refactor** `~\Common\Field\Collection` interface and `~\Core\Field\Collection` implementation.
* **Refactor** `~\Core\Field\Field` implementation.
* **Refactor** `~\Common\Route\Collection` interface and `~\Core\Route\Collection` implementation.
* **Refactor** `~\Core\Route\Options` implementation.
* **Deprecate** `~\Common\Endpoint\Handler` interface in favor of `~\Common\Endpoint\RequestHandler`.
* **Deprecate** `~\Core\Field\Definition` implementation in favor of `~\Core\Field\Field`.
* **Deprecate** `~\Common\Route\Options` interface in favor of `~\Common\Arguments`.

## 1.0.0

* Initial release.
