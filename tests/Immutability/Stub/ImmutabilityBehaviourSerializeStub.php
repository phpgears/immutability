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
 * ImmutabilityBehaviour trait serialize stub class.
 */
class ImmutabilityBehaviourSerializeStub
{
    use ImmutabilityBehaviour;

    /**
     * @var string
     */
    protected $parameter;

    /**
     * ImmutabilityTraitStub constructor.
     *
     * @param string $parameter
     */
    public function __construct(string $parameter)
    {
        $this->assertImmutable();

        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * Serialize.
     *
     * @return string[]
     */
    public function __sleep(): array
    {
        return ['parameter'];
    }

    /**
     * Unserialize.
     *
     * @param mixed $serialized
     */
    public function __wakeup(): void
    {
        $this->assertImmutable();
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedInterfaces(): array
    {
        return [static::class];
    }
}
