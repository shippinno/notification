<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class NotificationId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param NotificationId $other
     * @return bool
     */
    public function equals(NotificationId $other): bool
    {
        return $this->id() === $other->id();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strval($this->id());
    }
}
