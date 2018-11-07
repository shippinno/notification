<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;
use Shippinno\Notification\Domain\Model\Body;

class NotificationBodyType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform = null)
    {
        /** @var Body $value */
        return $value->body();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform = null)
    {
        return new Body($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification_body';
    }
}
