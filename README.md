# Notification

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/shippinno/notification/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/shippinno/notification/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/shippinno/notification/badges/build.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/build-status/master)

## Installation

```sh
$ composer require shippinno/notification
```

## Basic Usage

### Create and send a notification

Create a `Notification` with a `Destination` and send it through a `Gateway`. 

```php
use Shippinno\Email\SwiftMailer\SwiftMailerSendEmail;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationNotSentException;
use Shippinno\Notification\Infrastructure\Domain\Model\EmailGateway;

$notification = new Notification(
    new EmailDestination(
        [new EmailAddress('to@example.com')],
    ),
    new Subject('Hello'),
    new Body('This is a notification.'),
    
);

$gateway = new EmailGateway(
    new SwiftMailerSendEmail(...),
    new EmailAddress('from@example.com')
);

try {
    $gateway->send($notification);
} catch (NotificationNotSentException $e) {
    // ...
}
```

`Gateway` has to be compatible with the `Destination` (check `Destination::sendsToDestination(Destination $destination)`). In the case above, we assume `EmailGateway` accepts notifications with `EmailDestination`. 

### Persist notifications

Use `NotificationRepository` to persist notifications on your database.

If you use `DoctrineNotificationRepository` and set `$isPrecocious` attribute to `true`, you do not have to do `EntityManager::flush()`.

```php
$repository = new DoctrineNotificationRepository($em, $class, true); // $isPrecocious === true
$repository->add($notification); // Already flushed.
```

You can retrieve fresh (not sent or failed) notifications to send them.

```php
$notifications = $repository->freshNotifications();
```

Working with persisted notifications, you should want to mark them as sent or failed after trying to send.

If your `DoctrineNotificationRepository` is precocious, calling `persist()` will flush immediately.

```php
try {
    $gateway->send($notification);
    $notification->markSent();
} catch (NotificationNotSentException $e) {
    $notification->markFailed($e->__toString()); // mark it failed with the reason
} finally {
    $repository->persist($notification);
}
```

## Advanced usage

### Using templates

Let’s say you have [Liquid](https://shopify.github.io/liquid/) templates like:

```sh
$ tree -d /templates
/templates
|-- hello__subject.liquid 
`-- hello__body.liquid
$
$ cat /templates/hello.subject.liquid
Hello, {{ you }} !!
$
$ cat /templates/hello.body.liquid
Good bye, {{ her }} :)
```

Then you can create notifications using those templates with `TemplateNotificationFactory`.

```php
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Shippinno\Template\Liquid;
use Shippinno\Notification\Domain\Model\TemplateNotificationFactory;

$template = new Liquid(new Filesystem(new Local('/templates')));
$factory = new TemplateNotificationFactory($template);
$notification = $factory->create(
    'hello', // template name
    ['you' => 'Shippinno', 'her' => 'Jessica']), // variables for the template
    new EmailDestination([new EmailAddress('to@example.com')])
);
$notification->subject()->subject(); // => 'Hello Shippinno !!'
$notification->body()->body(); // => 'Good bye Jessica :)'
```

Check out [shippinno/template](https://github.com/shippinno/template-php) for more details how the template things work.

### Gateway routing

`SendNotification` service routes a notification to and send it through a gateway designated on `GatewayRegistry`.

```php
use Shippinno\Notification\Domain\Model\SendNotification;
use Shippinno\Notification\Domain\Model\GatewayRegistry;

$gatewayRegistry = new GatewayRegistry;
$gatewayRegistry->set('EmailDestination', new EmailGateway(...));
$gatewayRegistry->set('SlackChannelDestination', new SlackGateway(...));

$emailNotification = new Notification(new EmailDestination(...), ...);
$slackChannelNotification = new Notification(new SlackChannelDestination(...), ...);

$sendNotifiation = new SendNotification($gatewayRegistry);
$sendNotification->execute($emailNotification); // will send an email
$sendNotification->execute($slackChannelNotification); // will send a message to the Slack channel
```
