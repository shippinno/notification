<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class Body
{
    /**
     * @var string
     */
    private $body;

    /**
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }
}
