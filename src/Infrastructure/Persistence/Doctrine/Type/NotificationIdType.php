<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Type;
use Shippinno\Notification\Domain\Model\NotificationId;

class NotificationIdType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform = null)
    {
        // For Doctrine 2.5
        if (is_int($value)) {
            return $value;
        }

        /** @var NotificationId $value */
        return $value->id();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform = null)
    {
        return new NotificationId((int) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification_id';
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }
}
