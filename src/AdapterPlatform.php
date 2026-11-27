<?php

declare(strict_types=1);

namespace PhpDb\Sql92;

use Override;
use PhpDb\Adapter\Exception;
use PhpDb\Adapter\Platform\AbstractPlatform;
use PhpDb\Sql\Platform\Platform;
use PhpDb\Sql\Platform\StatementFactoryInterface;

use function addcslashes;

/**
 * Standard SQL conformance platform.
 *
 * Quotes identifiers by the SQL-92 rules and renders statements with the standard statement
 * factory, so a statement can be rendered without a driver present. It cannot quote values
 * without one: a value needs the driver that will escape it, and guessing here would be the
 * unsafe path this platform exists to avoid.
 *
 * The test suite extends it to stand in for a dialect, which is why it is not final.
 *
 * @internal
 *
 * @mago-expect analysis:class-must-be-final
 */
class AdapterPlatform extends AbstractPlatform
{
    final public const string PLATFORM_NAME = 'SQL92';

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getName(): string
    {
        return self::PLATFORM_NAME;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getStatementFactory(): StatementFactoryInterface
    {
        return new Platform();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException When no driver is available to quote safely.
     */
    #[Override]
    public function quoteValue(string $value): string
    {
        if (null === ($this->driver ?? null)) {
            throw Exception\RuntimeException::forVulnerablePlatformQuote(static::class, __METHOD__);
        }

        return '\'' . addcslashes($value, characters: "\x00\n\r\\'\"\x1a") . '\'';
    }
}
