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
class ImmutabilityBehaviourCheckFromMethodStub implements ImmutabilityBehaviourStubInterface
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
        $this->parameter = $parameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * Check constraints.
     */
    public function check(): void
    {
        $this->assertImmutable();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return \serialize($this->parameter);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $serialized
     */
    public function unserialize($serialized): void
    {
        $this->assertImmutable();

        $this->parameter = \unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedInterfaces(): array
    {
        return [self::class];
    }
}
