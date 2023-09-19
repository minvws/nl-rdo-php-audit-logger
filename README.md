# nl-rdo-audit-logger

This package provides a generic logging service for the RDO platform. It allows to easily
log events to the database, syslog or other destinations.

Log events can be logged with or without PII data. PII data is data that can be used
to identify a person. Depending on the logging destination, it can send the PII data or not.

Data can be automatically encrypted with a pub/priv keypair so that logging can be
written, but not directly read.

This package is a generic module that isn't directly coupled to a framework, however, there 
are other packages that provide a framework specific implementation.

## Installation

### Composer

Install the package through composer. Since this is currently a private package, you must
enable the repository in your `composer.json` file:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:minvws/nl-rdo-audit-logger.git"
    }
  ]
```

After that, you can install the package:

```bash
$ composer require minvws/audit-logger
```

### Configuration

#### PSR logging

With the PSRLogger you can log through any PSR compatible logger. This makes it easy to 
log to any destination like syslog, or monolog.

Audit lines can optionally be encrypted so they can only be read by the application that
has the private key.

To generate a new keypair, use the following code:

```php
$kp = sodium_crypto_box_keypair();
$pubkey = sodium_crypto_box_publickey($kp);
$privkey = sodium_crypto_box_secretkey($kp);

echo "Public key: " . base64_encode($pubkey) . "\n";
echo "Private key: " . base64_encode($privkey) . "\n";
```

#### Logging full request

The option `log_full_request` can be used to log the full HTTP request. Any sensitive information
like passsords, tokens etc should be stripped from the request. However, there is no guarantee that
all sensitive information will be stripped if the naming is different.

However, this configuration option is NOT implemented. You MUST call `logFullRequest()` on the event
in order to log the full request.

### Usage:

To use the logger, inject or resolve the `LogService` class. This class has a single method:

```php

  $logger = app(LogService::class);
  $logger->log((new UserLoginLogEvent())
      ->asExecute()
      ->withActor($user)
      ->withData(['foo' => 'bar'])
      ->withPiiData(['bar' => 'baz'])
      ->withFailed(true, 'invalid login')
  );

```

To create an event, you can use the fluent interface to create the event. Two elements are always required:
the action code and the actor.

The action code is a string that describes the action that was performed. This could be 'create', 'delete', 'update', 'read' or 'execute'.
These are defined with the `->asCreate()`, `->asDelete()`, `->asUpdate()`, `->asRead()` and `->asExecute()` methods.

Next, we need to define the actor that performed the action. This can be a user, a client or a system. This is done with the `->withActor()` method.
It will need a user that implements the `LoggableUser` interface.

The event can have some additional fields:

`->withTarget` can be used to define the target of the action. This can be a user, a client or a system. This can be empty if the actor and target
are the same, but different when an actor performs an action onto a target (for instance, an admin (actor) creates a regular user (target)).

`->withData` can be used to add additional data to the event. This can be any data that is relevant to the event.

`->withPiiData` can be used to add PII data to the event. This can be any data that is relevant to the event and contains PII data. Depending on
the logging configuration, this data might or might not be send to a logger destination.

`->withFailed` can be used to mark the event as failed. This will add the `failed` field to the event and set it to `true`. The second parameter
can be used to add a reason for the failure.

`->withSource` can be used to add a source to the event. This can be any string that describes the source of the event. It's often nothing more
than the name of the application (`->withSource(config('app.name'))`).

`->withEventCode` can be used to add an event code to the event. This can be any string that describes the event. Often it's already defined by the
event class, but it can be overridden.

`->logFullRequest` can be used to log the full HTTP request. Any sensitive information like passsords, tokens etc will be stripped from the request.

# Events

Each event has a unique event code. This code is used to identify the event and can be used to filter events in your logs. For rabbitMQ logging,
each event will have a unique routing key that can be used to route events to different queues.

| Class                            | Event code                     | routing key                       | Description                       |
|----------------------------------|--------------------------------|-----------------------------------|-----------------------------------|
| DeclarationLogEvent              | 080001                         | declaration                       | Declaration event                 |
| LogAccessEvent                   | 080002                         | log_access                        | Accessing logs                    |
| VerificationCodeDisabledLogEvent | 080003                         | verification_code_disabled        | Disabled verification code        |
| RegistrationLogEvent             | 080004                         | registration                      | Registration event                |
| ------                           | --------                       | ------------------------------    | --------------------------------- |
| AccountChangeLogEvent            | 090001                         | account_change                    | generic user account changes      |
| UserCreatedLogEvent              | 090002                         | user_created                      | created new user                  |
| ResetCredentialsLogEvent         | 090003                         | reset_credentials                 | reset user credentials            |
| ActivateAccountLogEvent          | 090004                         | activate_account                  | Activate account                  |
| AdminPasswordResetLogEvent       | 090005                         | admin_password_reset              | Admin password reset              |
| AmpUploadEvent                   | 090006                         | amp_upload                        | AMP upload                        |
| ----                             | ----------                     | ------------------------------    | --------------------------------- |
| OrganisationCreatedLogEvent      | 090012                         | organisation_created              | Created new organisation          |
| OrganisationChangedLogEvent      | 090013                         | organisation_changed              | Updated organisation (name)       |
| --                               | ------------                   | ------------------------------    | --------------------------------- |
| AccountChangeLogEvent[^1]        | 900101                         | account_change                    | changed user data                 |
| AccountChangeLogEvent            | 900102                         | account_change                    | changed roles                     |
| AccountChangeLogEvent            | 900103                         | account_change                    | changed timeslot                  |
| AccountChangeLogEvent            | 900104                         | account_change                    | changed active enabled/disabled   |
| AccountChangeLogEvent            | 900105                         | account_change                    | reset credentials                 |
| --------------                   | ------------------------------ | --------------------------------- | --------------------------------- |
| AccountChangeLogEvent            | 900201                         | account_change                    | changed kvtb user data            |
| AccountChangeLogEvent            | 900202                         | account_change                    | changed kvtb roles                |
| AccountChangeLogEvent            | 900203                         | account_change                    | reset kvtb credentials            |
| --------------                   | ------------------------------ | --------------------------------- | --------------------------------- |
| UserLoginLogEvent                | 091111                         | user_login                        | user login                        |
| UserLogoutLogEvent               | 092222                         | user_logout                       | user logout                       |
| UserLoginTwoFactorFailedEvent    | 093333                         | user_login_two_factor_failed      | user login 2fa failed             |

[^1]: This event is used for all account changes. The event code is used to identify the type of change. Set this with the `->withEventCode()` method.

## Creating custom events

Creating a custom event is easy. You can create a new class that extends the `GeneralLogEvent` class. 

```php

  class MyCustomEvent extends GeneralLogEvent
  {
      public const EVENT_CODE = '991414';
      public const EVENT_KEY = 'my_custom_event';
  }

```
