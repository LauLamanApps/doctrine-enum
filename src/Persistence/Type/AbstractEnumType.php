<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineEnum\Persistence\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\Type;
use Exception;
use Werkspot\Enum\Util\ClassNameConverter;

abstract class AbstractEnumType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, $this->getValues());

        if ($platform instanceof SqlitePlatform) {
            return 'TEXT CHECK( ' . $fieldDeclaration['name'] . ' IN (' . implode(', ', $values) . ') )';
        }

        return 'ENUM(' . implode(',', $values) . ") COMMENT '(DC2Type:" . $this->getName() . ")'";
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return $this->toPhpValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException("Invalid '" . $this->getName() . "' value.");
        }

        return $value;
    }

    private static function getValidOptions()
    {
        $class = get_called_class();
        // Work around private constructor... without needing to do it the hard way be initializing the factory etc
        $serialized = 'O:' . strlen($class) . ":\"$class\":0:{}";
        $instance = unserialize($serialized);

        return $instance->getValues();
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, self::getValidOptions(), true);
    }

    public static function validate($value)
    {
        if (!self::isValid($value)) {
            throw new Exception('Invalid value for ' . ClassNameConverter::stripNameSpace(get_called_class()) . ": '" . $value . "'");
        }

        return true;
    }

    public function getName()
    {
        $class = ClassNameConverter::stripNameSpace(get_class($this));

        return ClassNameConverter::convertClassNameToServiceName($class);
    }

    /**
     * An array of the possible values.
     *
     * @return array
     */
    abstract protected function getValues();

    abstract protected function toPhpValue($databaseValue, AbstractPlatform $platform);
}
