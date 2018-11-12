<?php

namespace Shippinno\Notification\Domain\Model;

use Faker\Factory;
use Tanigami\ValueObjects\Web\EmailAddress;

class NotificationBuilder
{
    /**
     * @var Destination
     */
    private $destination;

    /**
     * @var Subject
     */
    private $subject;

    /**
     * @var Body
     */
    private $body;

    /**
     * @var null|DeduplicationKey
     */
    private $deduplicationKey;

    /**
     * @var null|array
     */
    private $metadata;

    /**
     * @return void
     */
    private function __construct()
    {
        $faker = Factory::create();
        $this->destination = new EmailDestination([new EmailAddress($faker->email)]);
        $this->subject = new Subject($faker->sentence(5));
        $this->body = new Body($faker->text(300));
        $this->deduplicationKey = null;
        $this->metadata = null;
    }

    /**
     * @return NotificationBuilder
     */
    public static function notification(): NotificationBuilder
    {
        return new NotificationBuilder;
    }

    /**
     * @param Destination $destination
     * @return NotificationBuilder
     */
    public function withDestination(Destination $destination): NotificationBuilder
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @param Subject|string $subject
     * @return NotificationBuilder
     */
    public function withSubject($subject): NotificationBuilder
    {
        if (is_string($subject)) {
            $subject = new Subject($subject);
        }
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param Body|string $body
     * @return NotificationBuilder
     */
    public function withBody($body): NotificationBuilder
    {
        if (is_string($body)) {
            $body = new Body($body);
        }
        $this->body = $body;

        return $this;
    }

    /**
     * @param DeduplicationKey|string $deduplicationKey
     * @return NotificationBuilder
     */
    public function withDeduplicationKey($deduplicationKey): NotificationBuilder
    {
        if (is_string($deduplicationKey)) {
            $deduplicationKey = new DeduplicationKey($deduplicationKey);
        }
        $this->deduplicationKey = $deduplicationKey;

        return $this;
    }

    /**
     * @param array $metadata
     * @return NotificationBuilder
     */
    public function withMetadata(array $metadata): NotificationBuilder
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return Notification
     */
    public function build(): Notification
    {
        return new Notification(
            $this->destination,
            $this->subject,
            $this->body,
            $this->deduplicationKey,
            $this->metadata
        );
    }
}
