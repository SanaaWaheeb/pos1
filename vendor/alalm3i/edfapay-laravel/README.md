# Laravel's integration with EdfaPay payment gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alalm3i/edfapay-laravel.svg?style=flat-square)](https://packagist.org/packages/alalm3i/edfapay-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/alalm3i/edfapay-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alalm3i/edfapay-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/alalm3i/edfapay-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/alalm3i/edfapay-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alalm3i/edfapay-laravel.svg?style=flat-square)](https://packagist.org/packages/alalm3i/edfapay-laravel)

This is an easy way to integrate with [EdfaPay](http://edfapay.com) and get payment link correctly.

You can find EdfaPay API documentation [here](https://edfapay.com/api/EdfapayCheckout.html#CallbackNotification)


## Installation

You can install the package via composer:

```bash
composer require alalm3i/edfapay-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="edfapay-laravel-config"
```

This is the contents of the published config file:

```php
return [
    'merchant_key' => env('EDFA_PAY_MERCHANT_KEY', null),
    'merchant_password' => env('EDFA_PAY_PASSWORD', null),
    'return_url' => env('EDFA_PAY_RETURN_URL', null),
];
```



## Usage

```php
 $response = \alalm3i\EdfaPay\Facades\EdfaPay::paymentURL([
        'order_id' => 'a001',
        'order_amount' => '10',
        'order_description' => 'description',
        'payer_first_name' => 'customer',
        'payer_last_name' => 'name',
        'payer_email' => 'nab@eee.com',
        'payer_mobile' => '966565555555',
        'payer_ip_address' => '176.44.76.222',
    ])->generate();
    
//$response = https://pay.edfapay.com/merchant/checkout/.....
```
This package cover generating the payment link only.

### ToDo

1. [x] Generate payment URL
2. [ ] Handle payment notifications
3. [ ] Handle route for notification webhook

You can extend `alalm3i\EdfaPay\EdfaPayNotification` class to utilize it as a wrapper with some useful getters for notification callback payload. Just initialize it with the response object received from EdfaPay.



## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Nabeel Alalmai](https://github.com/alalm3i)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
