# Notification

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/shippinno/notification/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/shippinno/notification/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/shippinno/notification/badges/build.png?b=master)](https://scrutinizer-ci.com/g/shippinno/notification/build-status/master)

## Installation

```sh
$ composer require shippinno/notification
```

## Usage

### Create a notification using a template

```sh
$ tree -d /templates
/templates
|-- hello.subject.liquid 
`-- hello.body.liquid
$
$ cat /templates/hello.subject.liquid
Hello, {{ you }} !!
$
$ cat /templates/hello.body.liquid
Good bye, {{ her }} :)
```

```php
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Shippinno\Template\Liquid;

$template = new Liquid(new Filesystem(new Local('/templates')));
$factory = new TemplateNotificationFactory($template);
$notification = $factory->createFromTemplate(
    'hello', // template name
    ['you' => 'Shippinno', 'her' => 'Jessica']), // variables for the template
    new EmailDestination([new EmailAddress('to@example.com')])
);
$notification->subject()->subject(); // => 'Hello Shippinno !!'
$notification->body()->body(); // => 'Good bye Jessica :)'
```
