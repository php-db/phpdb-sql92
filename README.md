# phpdb-sql92

The standard SQL conformance platform for [php-db/phpdb](https://github.com/php-db/phpdb).

It quotes identifiers by the SQL-92 rules and renders statements with phpdb's standard statement
factory, so a statement can be rendered where no driver is present. That is what conformance
testing needs: a platform to render through, without a database behind it.

It cannot quote values. A value has to be escaped by the driver that will execute it, and this
platform has none, so `quoteValue()` throws rather than guess. Subclass it and supply a driver when
a test needs rendered values.

## Installation

```bash
composer require --dev php-db/phpdb-sql92
```

## Usage

```php
use PhpDb\Sql\Select;
use PhpDb\Sql92\AdapterPlatform;

$select = (new Select('users'))->columns(['id']);
$sql    = $select->getSqlString(new AdapterPlatform());

// SELECT "users"."id" AS "id" FROM "users"
```

Rendering a statement that carries values needs a platform that can quote them:

```php
use PhpDb\Adapter\Driver\DriverInterface;
use PhpDb\Sql92\AdapterPlatform;

final class TrustingPlatform extends AdapterPlatform
{
    public function __construct(protected DriverInterface $driver) {}

    public function quoteValue(string $value): string
    {
        return $this->quoteTrustedValue($value);
    }
}
```

## License

BSD-3-Clause. See [LICENSE](LICENSE).
