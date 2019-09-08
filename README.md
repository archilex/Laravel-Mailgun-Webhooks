# Laravel Mailgun Webhooks

This package, once completed, will allow you to quick and easily integrate your Laravel application with Mailgun Webhooks thus allowing you to track the outgoing email status for each individual user.

- Log when an email was delivered to a specific user
- Log when an email failed to send to a specific user
- Etc...

This may be useful information you want to display to your end users. This might be useful information you want to display to a certain subset of users (Managers, Moderators, Admins, etc...).

Another optional feature is to enable alerts. Set it up so that you receive an alert when an email fails or when there is a spam complaint.

## Installation Instructions

Lets begin the installation. First you'll need to install the package with composer.

`composer require`

Next, you will want to publish the configuration and view files.

`php artisan vendor:publish --tag=mailgun_webhook_config`

`php artisan vendor:publish --tag=mailgun_webhook_view`

Here are additional fields you'll need to add to your dotenv file:

```
MAILGUN_WEBHOOKS_ALERTS_TO=
MAILGUN_WEBHOOKS_ALERTS_FROM_EMAIL=
MAILGUN_WEBHOOKS_ALERTS_FROM_NAME=
MAILGUN_WEBHOOKS_TRIGGER_DELIVERED=false
MAILGUN_WEBHOOKS_TRIGGER_OPENED=false
MAILGUN_WEBHOOKS_TRIGGER_PERM_FAILURE=true
MAILGUN_WEBHOOKS_TRIGGER_SPAM=true
MAILGUN_WEBHOOKS_TRIGGER_TEMP_FAILURE=true
MAILGUN_WEBHOOKS_TRIGGER_UNSUBSCRIBE=true
```

By default - we reference the App\Users model to form the relationship. If you use a custom model or have made changes to the User model - you may need to add and edit these variables to your dotenv as well:

```
MAILGUN_WEBHOOKS_USER_TABLE_NAME=users
MAILGUN_WEBHOOKS_USER_TABLE_EMAIL=email
MAILGUN_WEBHOOKS_USER_TABLE_KEY=id
MAILGUN_WEBHOOKS_USER_TABLE_FPQN=App\Users
```

After you've completed the configuration - lets re-cache it:

`php artisan config:cache`

Now, you'll need to run the database migrations:

`php artisan migrate`

And, finally, you'll need to add a middleware to the route middleware group. In `app\Http\Kernal.php` add the following line to the `routeMiddleware` group:

`'mailgun_webhooks' => \Biegalski\LaravelMailgunWebhooks\Middleware\ValidateMailgunWebhook::class,`

You're all set! Now you just need to add the API endpoints to the webhooks in your Mailgun account.

## Add Webhooks In Mailgun

1. Login
2. Navigate to `Sending` -> `Webhooks`
3. Click `Add Webhook` button
4. Select the appropriate `Event Type` and input its corresponding API endpoint. Endpoints listed below:

| Event Type | Endpoint URL |
| ----------- | ----------- |
| Delivered Messages | https://domain.com/api/mg-webhooks/delivered-messages |
| Opens | https://domain.com/api/mg-webhooks/opened-messages |
| Permanent Failure | https://domain.com/api/mg-webhooks/permanent-failure |
| Spam Complaints | https://domain.com/api/mg-webhooks/spam-complaints |
| Temporary Failure | https://domain.com/api/mg-webhooks/temporary-failure |
| Unsubscribes | https://domain.com/api/mg-webhooks/unsubscribes |

*Replace "https://domain.com" with your applications URL.

## Usage

It starts collecting data and sending notifications after configuration is setup. You can dive deeper and extract or display this data. Usage steps for that coming soon!


## Affiliations

I have no affiliation with Laravel or Mailgun. Both are just frequently used in projects I am involved in and this package fills a need across various projects.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
