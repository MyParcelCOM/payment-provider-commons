<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Support;

use ReflectionClass;
use ReflectionException;

trait GetPrivatePropertyValue
{
    /**
     * @throws ReflectionException
     */
    private function getPrivatePropertyValue(object $object, string $property): mixed
    {
        $classReflection = new ReflectionClass($object);
        $propertyReflection = $classReflection->getProperty($property);
        /** @noinspection PhpExpressionResultUnusedInspection */
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }
}
