<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;
use Shippinno\Notification\Domain\Model\Body;

class NotificationBodyType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /** @var Body|null $value */
        return $value->body();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
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
