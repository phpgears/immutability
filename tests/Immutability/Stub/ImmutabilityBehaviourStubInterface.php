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
 * ImmutabilityBehaviourStubInterface.
 */
interface ImmutabilityBehaviourStubInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getParameter(): string;
}
