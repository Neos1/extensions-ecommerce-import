# Disclaimer

It's alpha version.

## Info

This package can be used to parse any source file (YML, CSV, XML) and convert it to abstract Entity, which can be easily imported
to any existing ecommerce solution.

See **test** folder for examples.

### Requirements

PHP version >= 5.6

## Quick Start

Add this lines to composer.json

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Neos1/extensions-ecommerce-import"
        }
    ],
    "require": {
      "whitebox/ecommerce-import": "^0.0"
    }
}

```

Run: `composer install`

## TODO

1. Implement CSV parser
2. Implement XML parser
3. Add some examples
4. Add integration examples

## License

Apache 2.0 License

Copyright (C) 2020 Neos
