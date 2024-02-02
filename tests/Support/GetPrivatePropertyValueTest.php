<?php

declare(strict_types=1);

namespace Tests\Support;

use MyParcelCom\Payments\Providers\Support\GetPrivatePropertyValue;
use PHPUnit\Framework\TestCase;

use ReflectionException;

use function PHPUnit\Framework\assertSame;

class GetPrivatePropertyValueTest extends TestCase
{
    use GetPrivatePropertyValue;

    /**
     * @throws ReflectionException
     */
    public function test_it_gets_private_property_value(): void
    {
        $object = new class {
            private string $property = 'value';
        };

        assertSame('value', $this->getPrivatePropertyValue($object, 'property'));
    }
}
