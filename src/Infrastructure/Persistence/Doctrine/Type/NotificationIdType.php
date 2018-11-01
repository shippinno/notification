<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use Shippinno\Notification\Domain\Model\NotificationId;

class NotificationIdType extends IntegerType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /** @var NotificationId $value */
        return $value->id();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
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
}
