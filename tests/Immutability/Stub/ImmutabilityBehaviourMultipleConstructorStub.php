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

namespace Gears\Immutability\Tests\Stub;

/**
 * ImmutabilityBehaviour trait stub class.
 */
class ImmutabilityBehaviourMultipleConstructorStub extends ImmutabilityBehaviourStub
{
    /**
     * Call constructor one more time.
     */
    public function callConstructor(): void
    {
        $this->__construct('');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedInterfaces(): array
    {
        return [self::class];
    }
}
