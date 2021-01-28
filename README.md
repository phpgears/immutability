[![PHP version](https://img.shields.io/badge/PHP-%3E%3D7.1-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability)
[![License](https://img.shields.io/github/license/phpgears/immutability.svg?style=flat-square)](https://github.com/phpgears/immutability/blob/master/LICENSE)

[![Build Status](https://img.shields.io/travis/com/phpgears/immutability.svg?style=flat-square)](https://travis-ci.com/github/phpgears/immutability)
[![Style Check](https://styleci.io/repos/148840927/shield)](https://styleci.io/repos/148840927)
[![Code Quality](https://img.shields.io/scrutinizer/g/phpgears/immutability.svg?style=flat-square)](https://scrutinizer-ci.com/g/phpgears/immutability)
[![Code Coverage](https://img.shields.io/coveralls/phpgears/immutability.svg?style=flat-square)](https://coveralls.io/github/phpgears/immutability)

[![Total Downloads](https://img.shields.io/packagist/dt/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability/stats)
[![Monthly Downloads](https://img.shields.io/packagist/dm/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability/stats)

# Object immutability guard for PHP

Truly object immutability in PHP is an impossible task by the nature of the language itself, carefully checking and protecting properties and methods visibility is the closest we can get at this moment in time

Crafting an object to make it as immutable as can be requires expertise, dedication and focus so no mutating mechanism slips through developer's hands into the object, that is the reason behind this library, provide a way to help with the tedious task of ensuring object immutability

## Installation

### Composer

```
composer require phpgears/immutability
```

## Usage

Require composer autoload file

```php
require './vendor/autoload.php';
```

`Gears\Immutability\ImmutabilityBehaviour` trait enforces you to avoid public properties and mutable methods in your class by calling `assertImmutable` method on object construction, `__wakeup` method and `unserialize` methods if Serializable interface is implemented

This mentioned behaviour let alone would run your objects completely useless, you must provide an implementation of the abstract method `getAllowedInterfaces`, returning a list of interfaces whose public methods will be allowed in your class. Although you can also provide class names, as it can prove useful in some special cases, it is _highly discouraged_ and should be used sparsely

Few PHP magic methods are allowed to be defined as public, namely `__construct`, `__destruct`, `__get`, `__isset`, `__sleep`, `__wakeup`, `__serialize`, `__unserialize`, `__toString`, `__set_state`, `__clone`, `__debugInfo`

```php
use Gears\Immutability\ImmutabilityBehaviour;

interface MyInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getParameter(): string;
}

final class MyClass implements MyInterface
{
    use ImmutabilityBehaviour;

    protected function __construct()
    {
        $this->assertImmutable();
    }

    /**
     * Static methods are allowed.
     */
    public static function instance(): self
    {
        return new self();
    }

    /**
     * Method allowed because it's defined in the interface.
     */
    public function getParameter(): string
    {
        return '';
    }

    /**
     * Method allowed because it's defined in the \Serializable.
     */
    public function serialize(): string
    {
        return '';
    }

    /**
     * Method allowed because it's defined in the \Serializable.
     */
    public function unserialize($serialized): void
    {
        $this->assertImmutable();

        // unserialize
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedInterfaces(): array
    {
        return [MyInterface::class];
    }
}
```

As a general rule, your immutable classes should be declared final. In case you really need inheritance, at least your implementation of `getAllowedInterfaces` method has to be declared final for every instantiable class

Although not mandatory it is advised to make your constructors _protected_ and force developers to create static named constructors

From now on you can focus on those methods defined in your interfaces and make sure they do not mutate your object, with the confidence no other developer would mutate your object by accident or intentionally

### Example

If you want to have a look at a working example implementations of immutable objects have a look at [phpgears/identity](https://github.com/phpgears/identity), [phpgears/dto](https://github.com/phpgears/dto) or [phpgears/cqrs](https://github.com/phpgears/cqrs)

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/immutability/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/immutability/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/immutability/blob/master/LICENSE) included with the source code for a copy of the license terms.
