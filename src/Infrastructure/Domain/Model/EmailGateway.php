<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Shippinno\Email\EmailNotSentException;
use Shippinno\Email\SendEmail;
use Shippinno\Email\SmtpConfiguration;
use Shippinno\Notification\Domain\Model\Destination;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationNotSentException;
use Tanigami\ValueObjects\Web\Email;
use Tanigami\ValueObjects\Web\EmailAddress;

class EmailGateway extends Gateway
{
    /**
     * @var SendEmail
     */
    protected $sendEmail;

    /**
     * @var EmailAddress
     */
    protected $from;

    /**
     * @var int
     */
    protected $maxAttempts;

    /**
     * @param SendEmail $sendEmail
     * @param EmailAddress $from
     * @param int $maxAttempts
     */
    public function __construct(SendEmail $sendEmail, EmailAddress $from, int $maxAttempts = 1)
    {
        $this->sendEmail = $sendEmail;
        $this->from = $from;
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(Notification $notification): void
    {
        /** @var EmailDestination $destination */
        $destination = $notification->destination();
        $email = new Email(
            $notification->subject()->subject(),
            $notification->body()->body(),
            $destination->from() ?? $this->from,
            $destination->to(),
            $destination->cc(),
            $destination->bcc()
        );
        try {
            $this->sendEmail->execute($email, $this->maxAttempts, $destination->smtpConfiguration());
        } catch (EmailNotSentException $e) {
            throw new NotificationNotSentException($notification, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendsToDestination(Destination $destination): bool
    {
        return $destination instanceof EmailDestination;
    }
}
