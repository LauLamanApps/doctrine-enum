# Doctrine Enum
This package holds helper classes to convert Enums to database values with Doctrine.


### Install

`# composer require laulamanapps/doctrine-enum`

### Usage

Create Enum as described [here](https://github.com/Werkspot/Enum/blob/master/README.md).

```php
# src/Enums/FooEnum.php

namespace YourAwesomeOrganisation\Enums;

use Werkspot\Enum\AbstractEnum;

/**
 * @method static FooEnum foo()
 * @method bool isFoo()
 * @method static FooEnum bar()
 * @method bool isBar()
 */
final class FooEnum extends AbstractEnum
{
    const FOO = 'foo';
    const BAR = 'bar';  
}
```

Create a specific doctrine type for that enum:

```php
# src/Doctrine/Persistence/Type/FooEnumType.php

namespace YourAwesomeOrganisation\Doctrine\Persistence\Type;

use YourAwesomeOrganisation\Enums\FooEnum;

final class FooEnumType extends AbstractOneOnOneEnumType
{
    protected function getEnumClass()
    {
        return FooEnum::class;
    }

    public function getName(): string
    {
        return 'enum_foo_type';
    }
}

```

Add type to doctrine config:

```yaml
# config/packages/doctrine.yaml

doctrine:
    dbal:
        types:
            enum_foo_type: YourAwesomeOrganisation\Doctrine\Persistence\Type\FooEnumType

```

Now use it in your Entities

```php
# src/Entity/MyData.php

use YourAwesomeOrganisation\Enums\FooEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class MyData
{
    /**
     * @var FooEnum
     *
     * @ORM\Column(type="enum_foo_type")
     */
    private $foo;
    
    public function setFoo(FooEnum $fooEnum): void
    {
        $this->foo = $fooEnum;
    }
    
    public function getFoo(): FooEnum
    {
        return $this->foo;
    }
}
```
Now doctrine will automatically convert ENUM <=====> database value
