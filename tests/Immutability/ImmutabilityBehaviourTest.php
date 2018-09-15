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

use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourMutableMethodStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourMutablePropertyStub;
use Gears\Immutability\Tests\Stub\ImmutabilityBehaviourStub;
use PHPUnit\Framework\TestCase;

/**
 * ImmutabilityBehaviour trait test.
 */
class ImmutabilityBehaviourTest extends TestCase
{
    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessageRegExp /.+\ImmutabilityBehaviourMutablePropertyStub should not have public properties$/
     */
    public function testMutableProperty(): void
    {
        new ImmutabilityBehaviourMutablePropertyStub('value');
    }

    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessageRegExp /Class .+\ImmutabilityBehaviourMutableMethodStub should not have public methods$/
     */
    public function testMutableMethod(): void
    {
        new ImmutabilityBehaviourMutableMethodStub('value');
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

    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessageRegExp /Class .+ properties cannot be mutated/
     */
    public function testInvalidMethodCall(): void
    {
        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownMethod();
    }

    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessageRegExp /Class .+ properties cannot be mutated/
     */
    public function testInvalidMethodSet(): void
    {
        $stub = new ImmutabilityBehaviourStub('value');

        $stub->unknownAttribute = '';
    }

    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessageRegExp /Class .+ properties cannot be mutated/
     */
    public function testInvalidMethodUnset(): void
    {
        $stub = new ImmutabilityBehaviourStub('value');

        unset($stub->unknownAttribute);
    }

    /**
     * @expectedException \Gears\Immutability\Exception\ImmutabilityViolationException
     * @expectedExceptionMessage Invocation is not allowed
     */
    public function testInvalidMethodInvoke(): void
    {
        $stub = new ImmutabilityBehaviourStub('value');

        $stub();
    }
}
