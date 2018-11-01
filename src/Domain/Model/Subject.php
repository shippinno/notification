<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class Subject
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @param string $subject
     */
    public function __construct(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function subject(): string
    {
        return $this->subject;
    }
}
