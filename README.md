[![PHP version](https://img.shields.io/badge/PHP-%3E%3D7.1-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability)
[![License](https://img.shields.io/github/license/phpgears/immutability.svg?style=flat-square)](https://github.com/phpgears/immutability/blob/master/LICENSE)

[![Build Status](https://img.shields.io/travis/phpgears/immutability.svg?style=flat-square)](https://travis-ci.org/phpgears/immutability)
[![Style Check](https://styleci.io/repos/xxxxxx/shield)](https://styleci.io/repos/xxxxxx)
[![Code Quality](https://img.shields.io/scrutinizer/g/phpgears/immutability.svg?style=flat-square)](https://scrutinizer-ci.com/g/phpgears/immutability)
[![Code Coverage](https://img.shields.io/coveralls/phpgears/immutability.svg?style=flat-square)](https://coveralls.io/github/phpgears/immutability)

[![Total Downloads](https://img.shields.io/packagist/dt/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability/stats)
[![Monthly Downloads](https://img.shields.io/packagist/dm/phpgears/immutability.svg?style=flat-square)](https://packagist.org/packages/phpgears/immutability/stats)

# Object immutability guard for PHP

Truly object immutability in PHP is impossible by the nature of the language, checking and protecting properties and methods visibility is the closest we can get at this moment in time

Crafting an object to make it as immutable as can be requires expertise, dedication and focus so no mutating mechanism slips through developer's hands into the object, that is the reason behind this library, provide a trait to help you with the tedious task of ensuring object immutability

## Installation

Best way to install is using [Composer](https://getcomposer.org/):

```
composer require phpgears/immutability
```

Then require_once the autoload file:

```php
require_once './vendor/autoload.php';
```

## Usage

ImmutabilityBehaviour trait enforces you to avoid public properties and not create any public methods (other than __construct) on your class by calling `checkImmutability` method on object construction

This mentioned behaviour let alone would run your objects completely useless so you must provide an implementation of the abstract method `getAllowedInterfaces`, returning a list of interfaces whose public methods will be allowed in your class. Although _discouraged_ you can also provide classes as it can prove useful in some cases

```php
use Gears\Immutability\ImmutabilityBehaviour;

interface MyInterface
{
    /**
     * @return string
     */
    public function getParameter(): string;
}

class MyObject implements MyInterface
{
    use ImmutabilityBehaviour;

    /**
     * MyObject class constructor.
     *
     * Constructors are always allowed.
     */
    public function __construct()
    {
        $this->checkImmutability();
    }

    /**
     * {@inheritdoc}
     *
     * This method is allowed because it's defined in the interface.
     */
    public function getParameter(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getAllowedInterfaces(): array
    {
        return [MyInterface::class];
    }
}
```

It is of **absolute importance** to either mark your class as final or mark your implementation of `getAllowedInterfaces` as final, not fulfilling this requirement will end up with child objects being able to override getAllowedInterfaces method and thus rendering immutability check useless

From now on you can focus only on those methods defined in your interfaces and ensure they do not mutate your object, this task is up to you only, with the confidence no other developer would mutate your object by accident or intentionally

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/immutability/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/immutability/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/immutability/blob/master/LICENSE) included with the source code for a copy of the license terms.
