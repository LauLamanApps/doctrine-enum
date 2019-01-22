<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineEnum\Persistence\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractOneOnOneEnumType extends AbstractEnumType
{
    protected function getValues()
    {
        $domainEnumClass = $this->getEnumClass();

        return $domainEnumClass::getValidOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value) && $this->allowsNullValues()) {
            return;
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    protected function toPhpValue($databaseValue, AbstractPlatform $platform)
    {
        if (is_null($databaseValue) && $this->allowsNullValues()) {
            return;
        }

        $type = $this->getEnumClass();

        return $type::get($databaseValue);
    }

    /**
     * In general an Enum is required and should have a value, but in some cases, because of Doctrine's implementation
     * we cannot enforce this, because Doctrine doesn't support it.
     *
     * @return bool
     */
    protected function allowsNullValues()
    {
        return false;
    }

    /**
     * The classname for which we need to map 1-on-1 to the database
     *
     * @return string
     */
    abstract protected function getEnumClass();
}
