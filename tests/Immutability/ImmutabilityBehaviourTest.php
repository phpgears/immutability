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
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourNotFinalStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourSerializeStub;
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
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches(
                '/^Class ".+\ImmutabilityBehaviourMutablePropertyStub" should not have public properties\.$/'
            );
        } else {
            $this->expectExceptionMessageRegExp(
                '/^Class ".+\ImmutabilityBehaviourMutablePropertyStub" should not have public properties\.$/'
            );
        }

        new ImmutabilityBehaviourMutablePropertyStub('value');
    }

    public function testMutableMethod(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+\ImmutabilityBehaviourMutableMethodStub" should not have public methods\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        new ImmutabilityBehaviourMutableMethodStub('value');
    }

    public function testNotFinal(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+\ImmutabilityBehaviourNotFinalStub"'
            . ' or getAllowedInterfaces method should be final\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        new ImmutabilityBehaviourNotFinalStub('value');
    }

    public function testMultipleConstructorCall(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class .+ was already checked for immutability\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        $stub = new ImmutabilityBehaviourMultipleConstructorStub('value');

        $stub->callConstructor();
    }

    public function testCheckFromMethod(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Immutability assertion available only through ".+" methods, called from ".+::check"\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        $stub = new ImmutabilityBehaviourCheckFromMethodStub('value');

        $stub->check();
    }

    public function testSingleCall(): void
    {
        $stubReflection = new \ReflectionClass(ImmutabilityBehaviourStub::class);

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourStub::class] ?? false;
        static::assertFalse($check);

        new ImmutabilityBehaviourStub('value');

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourStub::class] ?? false;
        static::assertTrue($check);

        new ImmutabilityBehaviourStub('value');

        $check = $stubReflection
                ->getStaticProperties()['immutabilityCheckMap'][ImmutabilityBehaviourMutableMethodStub::class] ?? false;
        static::assertFalse($check);
    }

    public function testCheckSerialization(): void
    {
        $stub = new ImmutabilityBehaviourSerializeStub('value');
        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:64:"Gears\Immutability\Tests\Stub\ImmutabilityBehaviourSerializeStub":1:'
                . '{s:9:"parameter";s:5:"value";}'
            : 'O:64:"Gears\Immutability\Tests\Stub\ImmutabilityBehaviourSerializeStub":1:'
                . "{s:12:\"\000*\000parameter\";s:5:\"value\";}";

        static::assertSame($serialized, \serialize($stub));
        static::assertSame('value', (\unserialize($serialized))->getParameter());
    }

    public function testCheckSerializable(): void
    {
        $stub = new ImmutabilityBehaviourStub('value');
        $serialized = 'C:55:"Gears\Immutability\Tests\Stub\ImmutabilityBehaviourStub":12:{s:5:"value";}';

        static::assertSame($serialized, \serialize($stub));
        static::assertSame('value', (\unserialize($serialized))->getParameter());
    }

    public function testInvalidMethodCall(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+" properties cannot be mutated\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownMethod();
    }

    public function testInvalidMethodSet(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+" properties cannot be mutated\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownAttribute = '';
    }

    public function testInvalidMethodUnset(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+" properties cannot be mutated\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

        $stub = new ImmutabilityBehaviourStub('value');

        unset($stub->unknownAttribute);
    }

    public function testInvalidMethodInvoke(): void
    {
        $this->expectException(ImmutabilityViolationException::class);
        $messageRegex = '/^Class ".+" invocation is not allowed\.$/';
        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches($messageRegex);
        } else {
            $this->expectExceptionMessageRegExp($messageRegex);
        }

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
