<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Type;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class Type
{
    public static function fromValue($value, bool $allowsNull): self
    {
        $type = \gettype($value);

        if ($type === 'object') {
            return new ObjectType(TypeName::fromQualifiedName(\get_class($value)), $allowsNull);
        }

        return self::fromName($type, $allowsNull);
    }

    public static function fromName(string $typeName, bool $allowsNull): self
    {
        switch (\strtolower($typeName)) {
            case 'null':
                return new NullType;

            case 'unknown type':
                return new UnknownType;

            case 'void':
                return new VoidType;

            case 'object':
            case 'boolean':
            case 'bool':
            case 'integer':
            case 'int':
            case 'real':
            case 'double':
            case 'float':
            case 'string':
            case 'array':
            case 'resource':
            case 'resource (closed)':
                return new SimpleType($typeName, $allowsNull);

            default:
                return new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull);
        }
    }

    abstract public function isAssignable(self $other): bool;

    abstract public function getReturnTypeDeclaration(): string;

    abstract public function allowsNull(): bool;
}
