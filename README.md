# Magento 2 API PHP-SDK

This is a very incomplete implementation of the Magento 2 API. Extended as needed. Will break often. PRs welcome.

## Installation

You can install the package via composer:

```bash
composer require yumdap/magento2-api
```

## Usage

``` php
use Yumdap\Magento2\Client;

// create client
$client = Client($api_url, $integration_token);

// get order
$order = $client->getOrderByIncrementId("100000012");

// create invoice for given order
$client->invoiceOrder($order);

// create shipment for that order
$client->shipOrder($order);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email mniess@gmail.com instead of using the issue tracker.

## Credits

- [Matthias Niess](https://github.com/dakira)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
