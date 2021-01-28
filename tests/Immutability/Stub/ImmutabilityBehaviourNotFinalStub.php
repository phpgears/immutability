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

use Gears\Immutability\ImmutabilityBehaviour;

/**
 * ImmutabilityBehaviour trait stub class.
 */
class ImmutabilityBehaviourNotFinalStub
{
    use ImmutabilityBehaviour;

    public function __construct()
    {
        $this->assertImmutable();
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedInterfaces(): array
    {
        return [self::class];
    }
}
