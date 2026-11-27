<?php

declare(strict_types=1);

namespace PhpDbTest\Sql92;

use PhpDb\Adapter\Driver\DriverInterface;
use PhpDb\Adapter\Exception\RuntimeException;
use PhpDb\Sql92\AdapterPlatform;
use PhpDb\Sql\Platform\Platform;
use PhpDb\Sql\Platform\StatementFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(AdapterPlatform::class)]
final class AdapterPlatformTest extends TestCase
{
    #[Test]
    public function namesThePlatform(): void
    {
        static::assertSame('SQL92', (new AdapterPlatform())->getName());
    }

    #[Test]
    public function quoteValueEscapesWhenASubclassSuppliesADriver(): void
    {
        $platform = new class($this->createStub(DriverInterface::class)) extends AdapterPlatform {
            public function __construct(
                protected DriverInterface $driver,
            ) {}
        };

        static::assertSame("'test\\'value'", $platform->quoteValue("test'value"));
    }

    #[Test]
    public function quoteValueListThrowsWithoutADriver(): void
    {
        $this->expectException(RuntimeException::class);

        (new AdapterPlatform())->quoteValueList(['value']);
    }

    #[Test]
    public function quoteValueThrowsWithoutADriver(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            RuntimeException::VULNERABLE_PLATFORM_QUOTE,
            AdapterPlatform::class,
            AdapterPlatform::class . '::quoteValue',
        ));

        (new AdapterPlatform())->quoteValue("Foo O'Bar");
    }

    #[Test]
    public function rendersStatementsWithTheStandardFactory(): void
    {
        $factory = (new AdapterPlatform())->getStatementFactory();

        static::assertInstanceOf(StatementFactoryInterface::class, $factory);
        static::assertInstanceOf(Platform::class, $factory);
    }
}
