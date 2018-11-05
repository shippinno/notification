<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Shippinno\Email\EmailNotSentException;
use Shippinno\Email\SendEmail;
use Shippinno\Notification\Domain\Model\Destination;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationNotSentException;
use Tanigami\ValueObjects\Web\Email;
use Tanigami\ValueObjects\Web\EmailAddress;
use Verraes\ClassFunctions\ClassFunctions;

class EmailGateway extends Gateway
{
    /**
     * @var SendEmail
     */
    private $sendEmail;

    /**
     * @var EmailAddress
     */
    private $from;

    /**
     * @param SendEmail $sendEmail
     * @param EmailAddress $from
     */
    public function __construct(SendEmail $sendEmail, EmailAddress $from)
    {
        $this->sendEmail = $sendEmail;
        $this->from = $from;
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
            $this->from,
            $destination->to(),
            $destination->cc(),
            $destination->bcc()
        );
        try {
            $this->sendEmail->execute($email);
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
