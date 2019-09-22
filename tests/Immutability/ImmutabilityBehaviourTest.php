<?php

/*
 * PHPGears immutability (https://github.com/phpgears/immutability).
 * Object immutability guard for PHP.
 *
 * @license MIT
 * @link https://github.com/phpgears/immutability
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

declare(strict_types=1);

namespace Gears\Immutability\Tests;

use Gears\Immutability\Exception\ImmutabilityViolationException;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourCheckFromMethodStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourDeprecatedStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourMultipleConstructorStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourMutableMethodStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourMutablePropertyStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourStub;
use PHPUnit\Framework\TestCase;

/**
 * ImmutabilityBehaviour trait test.
 */
class ImmutabilityBehaviourTest extends TestCase
{
    public function testMutableProperty(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class ".+\ImmutabilityBehaviourMutablePropertyStub" should not have public properties$/'
        );

        new ImmutabilityBehaviourMutablePropertyStub('value');
    }

    public function testMutableMethod(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class ".+\ImmutabilityBehaviourMutableMethodStub" should not have public methods$/'
        );

        new ImmutabilityBehaviourMutableMethodStub('value');
    }

    public function testMultipleConstructorCall(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class .+ was already checked for immutability$/'
        );

        $stub = new ImmutabilityBehaviourMultipleConstructorStub('value');

        $stub->callConstructor();
    }

    public function testCheckFromMethod(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Immutability check must be called from constructor or "unserialize" methods, called from ".+::check"$/'
        );

        $stub = new ImmutabilityBehaviourCheckFromMethodStub('value');

        $stub->check();
    }

    public function testSingleImmutabilityCheck(): void
    {
        $stubReflection = new \ReflectionClass(ImmutabilityBehaviourStub::class);

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourStub::class] ?? false;
        $this->assertFalse($check);

        new ImmutabilityBehaviourStub('value');

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourStub::class] ?? false;
        $this->assertTrue($check);

        new ImmutabilityBehaviourStub('value');

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourMutableMethodStub::class] ?? false;
        $this->assertFalse($check);
    }

    public function testSerializableImmutabilityCheck(): void
    {
        $immutable = new ImmutabilityBehaviourStub('value');

        $serialized = \serialize($immutable);

        $this->assertEquals($immutable, \unserialize($serialized));
    }

    public function testInvalidMethodCall(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class ".+" properties cannot be mutated$/'
        );

        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownMethod();
    }

    public function testInvalidMethodSet(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class ".+" properties cannot be mutated$/'
        );

        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownAttribute = '';
    }

    public function testInvalidMethodUnset(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Class ".+" properties cannot be mutated$/'
        );

        $stub = new ImmutabilityBehaviourStub('value');

        unset($stub->unknownAttribute);
    }

    public function testInvalidMethodInvoke(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $this->expectExceptionMessage('Invocation is not allowed');

        $stub = new ImmutabilityBehaviourStub('value');

        $stub();
    }

    public function testDeprecatedCheck(): void
    {
        new ImmutabilityBehaviourDeprecatedStub('value');

        static::assertEquals(
            'Calling the "checkImmutability()" method is deprecated. Use "assertImmutable()" method instead',
            \error_get_last()['message']
        );
    }
}
