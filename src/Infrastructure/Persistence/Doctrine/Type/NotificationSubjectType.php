<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Shippinno\Notification\Domain\Model\Subject;

class NotificationSubjectType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform = null)
    {
        /** @var Subject $value */
        return $value->subject();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform = null)
    {
        return new Subject($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification_subject';
    }
}
