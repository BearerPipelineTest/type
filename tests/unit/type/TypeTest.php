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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Type::class)]
#[UsesClass(CallableType::class)]
#[UsesClass(GenericObjectType::class)]
#[UsesClass(IterableType::class)]
#[UsesClass(NeverType::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class TypeTest extends TestCase
{
    #[DataProvider('valuesToNullableType')]
    public function testTypeMappingFromValue($value, bool $allowsNull, Type $expectedType): void
    {
        $this->assertEquals($expectedType, Type::fromValue($value, $allowsNull));
    }

    public function valuesToNullableType(): array
    {
        return [
            '?null'          => [null, true, new NullType],
            'null'           => [null, false, new NullType],
            '?integer'       => [1, true, new SimpleType('int', true, 1)],
            'integer'        => [1, false, new SimpleType('int', false, 1)],
            '?boolean-true'  => [true, true, new SimpleType('bool', true, true)],
            '?boolean-false' => [false, true, new SimpleType('bool', true, false)],
            'true'           => [true, false, new TrueType],
            'false'          => [false, false, new FalseType],
            '?object'        => [new stdClass, true, new ObjectType(TypeName::fromQualifiedName(stdClass::class), true)],
            'object'         => [new stdClass, false, new ObjectType(TypeName::fromQualifiedName(stdClass::class), false)],
        ];
    }

    #[DataProvider('namesToTypes')]
    public function testTypeMappingFromName(string $typeName, bool $allowsNull, $expectedType): void
    {
        $this->assertEquals($expectedType, Type::fromName($typeName, $allowsNull));
    }

    public function namesToTypes(): array
    {
        return [
            '?void'             => ['void', true, new VoidType],
            'void'              => ['void', false, new VoidType],
            '?null'             => ['null', true, new NullType],
            'null'              => ['null', true, new NullType],
            '?int'              => ['int', true, new SimpleType('int', true)],
            '?integer'          => ['integer', true, new SimpleType('int', true)],
            'int'               => ['int', false, new SimpleType('int', false)],
            'bool'              => ['bool', false, new SimpleType('bool', false)],
            'boolean'           => ['boolean', false, new SimpleType('bool', false)],
            'true'              => ['true', false, new TrueType],
            'false'             => ['false', false, new FalseType],
            'object'            => ['object', false, new GenericObjectType(false)],
            'real'              => ['real', false, new SimpleType('float', false)],
            'double'            => ['double', false, new SimpleType('float', false)],
            'float'             => ['float', false, new SimpleType('float', false)],
            'string'            => ['string', false, new SimpleType('string', false)],
            'array'             => ['array', false, new SimpleType('array', false)],
            'resource'          => ['resource', false, new SimpleType('resource', false)],
            'resource (closed)' => ['resource (closed)', false, new SimpleType('resource (closed)', false)],
            'unknown type'      => ['unknown type', false, new UnknownType],
            '?classname'        => [stdClass::class, true, new ObjectType(TypeName::fromQualifiedName(stdClass::class), true)],
            'classname'         => [stdClass::class, false, new ObjectType(TypeName::fromQualifiedName(stdClass::class), false)],
            'callable'          => ['callable', false, new CallableType(false)],
            '?callable'         => ['callable', true, new CallableType(true)],
            'iterable'          => ['iterable', false, new IterableType(false)],
            '?iterable'         => ['iterable', true, new IterableType(true)],
            'mixed'             => ['mixed', false, new MixedType],
            'never'             => ['never', false, new NeverType],
        ];
    }
}
