<?php

namespace Shippinno\Notification\Domain\Model;

use Ramsey\Uuid\Uuid;

class NotificationId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return NotificationId
     */
    public static function create(): NotificationId
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id();
    }
}

