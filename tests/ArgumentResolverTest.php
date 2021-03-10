<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests;

use PHPUnit\Framework\TestCase;
use Premier\ArgumentResolver\ArgumentMetadataFactory;
use Premier\ArgumentResolver\ArgumentResolver;
use Premier\ArgumentResolver\ArgumentValueResolverInterface;
use Premier\ArgumentResolver\Tests\Fixtures\Controller\NullableCallable;
use Premier\ArgumentResolver\Tests\Fixtures\Controller\VariadicCallable;

class ArgumentResolverTest extends TestCase
{
    private static ArgumentResolver $resolver;

    public static function setUpBeforeClass(): void
    {
        $factory = new ArgumentMetadataFactory();

        self::$resolver = new ArgumentResolver($factory);
    }

    public function testGetArguments(): void
    {
        $data = ['foo' => 'foo'];
        $callable = [new self(), 'callableWithFoo'];

        self::assertSame(['foo'], self::$resolver->getArguments($data, $callable), '->getArguments() returns an array of arguments for the callable');
    }

    public function testGetArgumentsReturnsEmptyArrayWhenNoArguments(): void
    {
        $data = [];
        $callable = [new self(), 'callableWithoutArguments'];

        self::assertSame([], self::$resolver->getArguments($data, $callable), '->getArguments() returns an empty array if the method takes no arguments');
    }

    public function testGetArgumentsUsesDefaultValue(): void
    {
        $data = ['foo' => 'foo'];
        $callable = [new self(), 'callableWithFooAndDefaultBar'];

        self::assertSame(['foo', null], self::$resolver->getArguments($data, $callable), '->getArguments() uses default values if present');
    }

    public function testGetArgumentsOverrideDefaultValueByRequestAttribute(): void
    {
        $data = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];
        $callable = [new self(), 'callableWithFooAndDefaultBar'];

        self::assertSame(['foo', 'bar'], self::$resolver->getArguments($data, $callable), '->getArguments() overrides default values if provided in the request attributes');
    }

    public function testGetArgumentsFromClosure(): void
    {
        $data = ['foo' => 'foo'];
        $callable = function ($foo) {
        };

        self::assertSame(['foo'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetArgumentsUsesDefaultValueFromClosure(): void
    {
        $data = ['foo' => 'foo'];
        $callable = function ($foo, $bar = 'bar') {
        };

        self::assertSame(['foo', 'bar'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetArgumentsFromInvokableObject(): void
    {
        $data = ['foo' => 'foo'];
        $callable = new self();

        self::assertSame(['foo', null], self::$resolver->getArguments($data, $callable));

        // Test default bar overridden by request attribute
        $data['bar'] = 'bar';

        self::assertSame(['foo', 'bar'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetArgumentsFromFunctionName(): void
    {
        $data = [
            'foo' => 'foo',
            'foobar' => 'foobar',
        ];
        $callable = __NAMESPACE__.'\callable_function';

        self::assertSame(['foo', 'foobar'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetArgumentsFailsOnUnresolvedValue(): void
    {
        $data = [
            'foo' => 'foo',
            'foobar' => 'foobar',
        ];
        $callable = [new self(), 'callableWithFooBarFoobar'];

        try {
            self::$resolver->getArguments($data, $callable);
            self::fail('->getArguments() throws a \RuntimeException exception if it cannot determine the argument value');
        } catch (\Exception $e) {
            self::assertInstanceOf(\RuntimeException::class, $e, '->getArguments() throws a \RuntimeException exception if it cannot determine the argument value');
        }
    }

    public function testGetVariadicArguments(): void
    {
        $data = [
            'foo' => 'foo',
            'bar' => ['foo', 'bar'],
        ];
        $callable = [new VariadicCallable(), 'action'];

        self::assertSame(['foo', 'foo', 'bar'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetVariadicArgumentsWithoutArrayInRequest(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $data = [
            'foo' => 'foo',
            'bar' => 'foo',
        ];
        $callable = [new VariadicCallable(), 'action'];

        self::$resolver->getArguments($data, $callable);
    }

    public function testGetArgumentWithoutArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $factory = new ArgumentMetadataFactory();
        $valueResolver = $this->createMock(ArgumentValueResolverInterface::class);
        $resolver = new ArgumentResolver($factory, [$valueResolver]);

        $valueResolver->expects(self::any())->method('supports')->willReturn(true);
        $valueResolver->expects(self::any())->method('resolve')->willReturn([]);

        $data = [
            'foo' => 'foo',
            'bar' => 'foo',
        ];
        $callable = [$this, 'callableWithFooAndDefaultBar'];
        $resolver->getArguments($data, $callable);
    }

    public function testIfExceptionIsThrownWhenMissingAnArgument(): void
    {
        $this->expectException(\RuntimeException::class);
        $data = [];
        $callable = [$this, 'callableWithFoo'];

        self::$resolver->getArguments($data, $callable);
    }

    public function testGetNullableArguments(): void
    {
        $std = new \stdClass();

        $data = [
            'foo' => 'foo',
            'bar' => $std,
            'last' => 'last',
        ];
        $callable = [new NullableCallable(), 'action'];

        self::assertSame(['foo', $std, 'value', 'last'], self::$resolver->getArguments($data, $callable));
    }

    public function testGetNullableArgumentsWithDefaults(): void
    {
        $data = ['last' => 'last'];
        $callable = [new NullableCallable(), 'action'];

        self::assertSame([null, null, 'value', 'last'], self::$resolver->getArguments($data, $callable));
    }

    public function __invoke($foo, $bar = null)
    {
    }

    public function callableWithFoo($foo)
    {
    }

    public function callableWithoutArguments()
    {
    }

    public function callableWithFooAndDefaultBar($foo, $bar = null)
    {
    }

    public function callableWithFooBarFoobar($foo, $bar, $foobar)
    {
    }
}

function callable_function($foo, $foobar)
{
}
