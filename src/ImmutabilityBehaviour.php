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

namespace Gears\Immutability;

use Gears\Immutability\Exception\ImmutabilityViolationException;

/**
 * Immutability check behaviour.
 */
trait ImmutabilityBehaviour
{
    /**
     * Class immutability checked map.
     *
     * @var bool[]
     */
    protected static $immutabilityCheckMap = [];

    /**
     * List of default allowed magic methods.
     *
     * @var string[]
     */
    protected static $allowedMagicMethods = [
        '__construct',
        '__destruct',
        '__get',
        '__isset',
        '__sleep',
        '__wakeup',
        '__toString',
        '__set_state',
        '__clone',
        '__debugInfo',
    ];

    /**
     * Single constructor call check.
     *
     * @var bool
     */
    private $alreadyConstructed = false;

    /**
     * Check immutability.
     *
     * @throws ImmutabilityViolationException
     */
    final protected function checkImmutability(): void
    {
        $this->checkConstructCall();

        $class = static::class;

        if (isset(static::$immutabilityCheckMap[$class])) {
            return;
        }

        $this->checkPropertiesAccessibility();
        $this->checkMethodsAccessibility();

        static::$immutabilityCheckMap[$class] = true;
    }

    /**
     * Check __construct method is called only once.
     *
     * @throws ImmutabilityViolationException
     */
    private function checkConstructCall(): void
    {
        if ($this->alreadyConstructed) {
            throw new ImmutabilityViolationException(\sprintf(
                'Method %s::__construct was already called',
                static::class
            ));
        }

        $this->alreadyConstructed = true;
    }

    /**
     * Check properties accessibility.
     *
     * @throws ImmutabilityViolationException
     */
    private function checkPropertiesAccessibility(): void
    {
        $publicProperties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        if (\count($publicProperties) !== 0) {
            throw new ImmutabilityViolationException(\sprintf(
                'Class %s should not have public properties',
                static::class
            ));
        }
    }

    /**
     * Check methods accessibility.
     *
     * @throws ImmutabilityViolationException
     */
    private function checkMethodsAccessibility(): void
    {
        $publicMethods = $this->getClassPublicMethods();
        \sort($publicMethods);

        $allowedPublicMethods = $this->getAllowedPublicMethods();

        foreach (static::$allowedMagicMethods as $magicMethod) {
            if (\array_search($magicMethod, $publicMethods, true) !== false) {
                $allowedPublicMethods[] = $magicMethod;
            }
        }

        \sort($allowedPublicMethods);

        if (\count($publicMethods) > \count($allowedPublicMethods)
            || \count(\array_diff($allowedPublicMethods, $publicMethods)) !== 0
        ) {
            throw new ImmutabilityViolationException(\sprintf(
                'Class %s should not have public methods',
                static::class
            ));
        }
    }

    /**
     * Get list of defined public methods.
     *
     * @return string[]
     */
    private function getClassPublicMethods(): array
    {
        return \array_filter(\array_map(
            function (\ReflectionMethod $method): string {
                return !$method->isStatic() ? $method->getName() : '';
            },
            (new \ReflectionObject($this))->getMethods(\ReflectionMethod::IS_PUBLIC)
        ));
    }

    /**
     * Get list of allowed public methods.
     *
     * @return string[]
     */
    protected function getAllowedPublicMethods(): array
    {
        $allowedInterfaces = \array_unique(\array_merge($this->getAllowedInterfaces(), [ImmutabilityBehaviour::class]));
        $allowedMethods = \array_merge(
            ...\array_map(
                function (string $interface): array {
                    return (new \ReflectionClass($interface))->getMethods(\ReflectionMethod::IS_PUBLIC);
                },
                $allowedInterfaces
            )
        );

        return \array_unique(\array_filter(\array_map(
            function (\ReflectionMethod $method): string {
                return !$method->isStatic() ? $method->getName() : '';
            },
            $allowedMethods
        )));
    }

    /**
     * Get a list of allowed interfaces to extract public methods from.
     *
     * @return string[]
     */
    abstract protected function getAllowedInterfaces(): array;

    /**
     * @param string  $method
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    final public function __call(string $method, array $parameters)
    {
        throw new ImmutabilityViolationException(\sprintf('Class %s properties cannot be mutated', static::class));
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws ImmutabilityViolationException
     */
    final public function __set(string $name, $value): void
    {
        throw new ImmutabilityViolationException(\sprintf('Class %s properties cannot be mutated', static::class));
    }

    /**
     * @param string $name
     *
     * @throws ImmutabilityViolationException
     */
    final public function __unset(string $name): void
    {
        throw new ImmutabilityViolationException(\sprintf('Class %s properties cannot be mutated', static::class));
    }

    /**
     * @throws ImmutabilityViolationException
     */
    final public function __invoke(): void
    {
        throw new ImmutabilityViolationException('Invocation is not allowed');
    }
}
