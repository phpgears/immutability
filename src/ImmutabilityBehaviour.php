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
        '__serialize',
        '__unserialize',
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
    private $immutabilityAlreadyChecked = false;

    /**
     * Alias of assertImmutable.
     *
     * @deprecated use assertImmutable instead
     */
    final protected function checkImmutability(): void
    {
        @\trigger_error(
            'Calling the "checkImmutability()" method is deprecated. Use "assertImmutable()" method instead',
            \E_USER_DEPRECATED
        );

        $this->assertImmutable();
    }

    /**
     * Assert object immutability.
     *
     * @throws ImmutabilityViolationException
     */
    final protected function assertImmutable(): void
    {
        $this->assertImmutabilitySingleCall();
        $this->assertImmutabilityCallConstraints();

        $class = static::class;
        if (!isset(static::$immutabilityCheckMap[$class])) {
            $this->assertImmutabilityFinal();
            $this->assertImmutabilityPropertyVisibility();
            $this->assertImmutabilityMethodVisibility();

            static::$immutabilityCheckMap[$class] = true;
        }
    }

    /**
     * Assert single call.
     *
     * @throws ImmutabilityViolationException
     */
    private function assertImmutabilitySingleCall(): void
    {
        if ($this->immutabilityAlreadyChecked) {
            throw new ImmutabilityViolationException(
                \sprintf('Class "%s" was already checked for immutability.', static::class)
            );
        }

        $this->immutabilityAlreadyChecked = true;
    }

    /**
     * Assert immutability check call constraints.
     *
     * @throws ImmutabilityViolationException
     */
    private function assertImmutabilityCallConstraints(): void
    {
        $stack = $this->getImmutabilityFilteredCallStack();

        $callingMethods = ['__construct', '__wakeup', '__unserialize'];
        if ($this instanceof \Serializable) {
            $callingMethods[] = 'unserialize';
        }

        if (!isset($stack[1]) || !\in_array($stack[1]['function'], $callingMethods, true)) {
            throw new ImmutabilityViolationException(\sprintf(
                'Immutability assertion available only through "%s" methods, called from "%s".',
                \implode('", "', $callingMethods),
                isset($stack[1]) ? static::class . '::' . $stack[1]['function'] : 'unknown'
            ));
        }
    }

    /**
     * Get filter call stack.
     *
     * @return mixed[]
     */
    private function getImmutabilityFilteredCallStack(): array
    {
        $stack = \debug_backtrace();

        while (\count($stack) > 0 && $stack[0]['function'] !== 'assertImmutable') {
            \array_shift($stack);
        }

        if (isset($stack[1]) && $stack[1]['function'] === 'checkImmutability') {
            \array_shift($stack);
        }

        return $stack;
    }

    /**
     * Assert final.
     *
     * @throws ImmutabilityViolationException
     */
    private function assertImmutabilityFinal(): void
    {
        $reflectionObject = new \ReflectionObject($this);

        if (!$reflectionObject->isFinal()) {
            $reflectionMethod = $reflectionObject->getMethod('getAllowedInterfaces');

            if (!$reflectionMethod->isFinal()) {
                throw new ImmutabilityViolationException(\sprintf(
                    'Class "%s" or getAllowedInterfaces method should be final.',
                    static::class
                ));
            }
        }
    }

    /**
     * Assert properties visibility.
     *
     * @throws ImmutabilityViolationException
     */
    private function assertImmutabilityPropertyVisibility(): void
    {
        $publicProperties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        if (\count($publicProperties) !== 0) {
            throw new ImmutabilityViolationException(
                \sprintf('Class "%s" should not have public properties.', static::class)
            );
        }
    }

    /**
     * Assert methods visibility.
     *
     * @throws ImmutabilityViolationException
     */
    private function assertImmutabilityMethodVisibility(): void
    {
        $publicMethods = $this->getImmutabilityClassPublicMethods();
        $allowedPublicMethods = $this->getImmutabilityAllowedPublicMethods();

        if (\count($publicMethods) > \count($allowedPublicMethods)
            || \count(\array_diff($publicMethods, $allowedPublicMethods)) !== 0
        ) {
            throw new ImmutabilityViolationException(
                \sprintf('Class "%s" should not have public methods.', static::class)
            );
        }
    }

    /**
     * Get list of defined public methods.
     *
     * @return string[]
     */
    private function getImmutabilityClassPublicMethods(): array
    {
        $publicMethods = \array_filter(\array_map(
            function (\ReflectionMethod $method): string {
                return !$method->isStatic() ? $method->getName() : '';
            },
            (new \ReflectionObject($this))->getMethods(\ReflectionMethod::IS_PUBLIC)
        ));

        \sort($publicMethods);

        return $publicMethods;
    }

    /**
     * Get list of allowed public methods.
     *
     * @return string[]
     */
    private function getImmutabilityAllowedPublicMethods(): array
    {
        $allowedInterfaces = \array_unique(\array_filter(\array_merge(
            $this->getAllowedInterfaces(),
            [ImmutabilityBehaviour::class]
        )));
        $allowedPublicMethods = \array_unique(\array_filter(\array_merge(
            static::$allowedMagicMethods,
            ...\array_map(
                function (string $interface): array {
                    return \array_map(
                        function (\ReflectionMethod $method): string {
                            return !$method->isStatic() ? $method->getName() : '';
                        },
                        (new \ReflectionClass($interface))->getMethods(\ReflectionMethod::IS_PUBLIC)
                    );
                },
                $allowedInterfaces
            )
        )));

        \sort($allowedPublicMethods);

        return $allowedPublicMethods;
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
        throw new ImmutabilityViolationException(\sprintf('Class "%s" properties cannot be mutated.', static::class));
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws ImmutabilityViolationException
     */
    final public function __set(string $name, $value): void
    {
        throw new ImmutabilityViolationException(\sprintf('Class "%s" properties cannot be mutated.', static::class));
    }

    /**
     * @param string $name
     *
     * @throws ImmutabilityViolationException
     */
    final public function __unset(string $name): void
    {
        throw new ImmutabilityViolationException(\sprintf('Class "%s" properties cannot be mutated.', static::class));
    }

    /**
     * @throws ImmutabilityViolationException
     */
    final public function __invoke(): void
    {
        throw new ImmutabilityViolationException(\sprintf('Class "%s" invocation is not allowed.', static::class));
    }
}
