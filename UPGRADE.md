# LUYA ADMIN MODULE UPGRADE

This document will help you upgrading from a LUYA admin module version into another. For more detailed informations about the breaking changes **click the issue detail link**, there you can examples of how to change your code.

## from 1.0 to 2.0 (in progress)

+ module propertys `recipient`, `slackToken` and `slackChannel` has been removed.
+ Adapters replace the current default mail and slack properties, therefore configure a MailAdapter and/or a SlackAdapter.

Old config:

```php
'errorapi' => [
    'class' => 'luya\errorapi\Module',
    'recipient' => ['errors@example.com'],
    'slackToken' => 'YOUR_SECRET_SLACK_TOKEN',
],
```

New config:

```php
'errorapi' => [
    'class' => 'luya\errorapi\Module',
    'adapters' => [
        [
            'class' => 'luya\errorapi\adapters\MailAdapter',
            'recipient' => ['errors@example.com'],
        ],
        [
            'class' => 'luya\errorapi\adapters\SlackAdapter',
            'token' => 'YOUR_SECRET_SLACK_TOKEN',
        ],
    ],
],
```