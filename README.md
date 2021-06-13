<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# Error API Module

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)
[![Latest Stable Version](https://poser.pugx.org/luyadev/luya-module-errorapi/v/stable)](https://packagist.org/packages/luyadev/luya-module-errorapi)
[![Total Downloads](https://poser.pugx.org/luyadev/luya-module-errorapi/downloads)](https://packagist.org/packages/luyadev/luya-module-errorapi)
[![Maintainability](https://api.codeclimate.com/v1/badges/ae9ba69fc2644b5ef9be/maintainability)](https://codeclimate.com/github/luyadev/luya-module-errorapi/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ae9ba69fc2644b5ef9be/test_coverage)](https://codeclimate.com/github/luyadev/luya-module-errorapi/test_coverage)
[![Build Status](https://travis-ci.org/luyadev/luya-module-errorapi.svg?branch=master)](https://travis-ci.org/luyadev/luya-module-errorapi)

For a solid and secure website, it is important to get notified about all the errors and exceptions that occur. This helps prevent unnoticed repeating errors and keeps customers happy as they won't have to complain.

With the Error Api module, you can send all exceptions to your personal Error Api and get notify by email or Slack. If an exception occurs on the customer website, you will be notified with the full error stack and a slack notification will be sent (if configured).

## Install Server

For the installation of modules Composer is required:

```
composer require luyadev/luya-module-errorapi
```

### Configuration

After installation via Composer include the module to your configuration file within the modules section.

```php
'modules' => [
    // ...
    'errorapi' => [
        'class' => 'luya\errorapi\Module',
        'adapters' => [
            [
                'class' => 'luya\errorapi\adapters\MailAdapter',
                'recipient' => ['errors@example.com'],
            ],
            [
                'class' => 'luya\errorapi\adapters\SlackAdapter',
                'token' => 'xyz.xyz.xyz.xyz',
            ],
            [
                'class' => 'luya\errorapi\adapters\SentryAdapter',
                'token' => 'YOUR_SENTRY_USER_AUTH_TOKEN',
                'organisation' => 'organisationslug',
                'team' => 'teamslug',
            ]
        ],
    ],
]
```

### Initialization

After successfully installation and configuration run the migrate, import and setup command to initialize the module in your project.

1.) Migrate your database.

```sh
./vendor/bin/luya migrate
```

2.) Import the module and migrations into your LUYA project.

```sh
./vendor/bin/luya import
```

> It is very important to run the `./vendor/bin/luya migrate` and `./vendor/bin/luya import` commands in order for these changes to take effect.

## Setup Client

To enable the error api for your website you need to configure the default LUYA error handler in the component section of your config file with the current setup server (error api):

```php
'components' => [
    // ...
    'errorHandler' => [
        'api' => 'https://example.com/errorapi', // where example is the domain you have setup error api above
        'transferException' => true,
    ],
]
```
