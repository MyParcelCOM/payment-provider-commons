# payment-provider-commons
Library with common classes used for constructing MyParcel.com payment provider integrations

## PHP 8
The minimum PHP version is `8.3`. To update dependencies on a system without PHP 8 use:
```shell
docker run --rm --mount type=bind,source="$(pwd)",target=/app composer:2 composer update
```

## Run tests
You can run the test through docker:
```shell
docker run --rm --mount type=bind,source="$(pwd)",target=/app -w /app php:8.3-cli vendor/bin/phpunit
```
