<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Shippinno\Notification\Domain\Model\DeduplicationKey;

class NotificationDeduplicationKeyType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform = null)
    {
        /** @var DeduplicationKey|null $value */
        return is_null($value) ? null : $value->key();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform = null)
    {
        return is_null($value) ? null : new DeduplicationKey($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification_deduplication_key';
    }
}
