<h1 align="center">Laravel Subify</h1>

<p align="center"><a href="https://packagist.org/packages/open-saas/laravel-subify"><img alt="Latest Version on Packagist" src="https://img.shields.io/packagist/v/open-saas/laravel-subify.svg?style=flat-square"></a>
<a href="https://github.com/open-saas/laravel-subify/actions/workflows/tests.yml"><img src="https://github.com/open-saas/laravel-subify/actions/workflows/tests.yml/badge.svg" alt="tests"></a>
<a href="https://github.com/open-saas/laravel-subify/actions/workflows/static-analysis.yml"><img src="https://github.com/open-saas/laravel-subify/actions/workflows/static-analysis.yml/badge.svg" alt="static analysis"></a>
<a href="https://github.com/open-saas/laravel-subify/actions/workflows/code-standards.yml"><img src="https://github.com/open-saas/laravel-subify/actions/workflows/code-standards.yml/badge.svg" alt="code standards"></a>
<a href="https://codecov.io/gh/open-saas/laravel-subify"><img src="https://codecov.io/gh/open-saas/laravel-subify/branch/develop/graph/badge.svg?token=9NUYY1E28D"/></a>

## About

A straightforward way to handle subscriptions in Laravel.

## Terminology

We have opted to use the term "benefit" rather than "feature" due to its broader scope. While a feature may be a benefit, benefits can include aspects such as quotas or limits.

Therefore, by referring to "benefits" we aim to encompass all elements subscribers receive by signing to a plan.

## Development Status

- [ ] Subscription Plans
  - [ ] Create, edit, and delete subscription plans
  - [ ] Free plans
  - [ ] Set billing intervals (monthly, yearly, etc.)
  - [ ] Define benefits and limits for each plan
  - [ ] Offer free trials and promotions
  - [ ] Multiple subscription plans per subscriber
  - [ ] Pricing and discounts
- [ ] Benefits
  - [ ] Create, edit, and delete benefits
  - [ ] Periodicity (for example, daily, weekly, monthly, etc.)
  - [ ] Consumable benefits (for example, 1000 API calls per month)
  - [ ] Non-consumable benefits (they should work like feature flags: either you have it or you don't)
  - [ ] Quota-based benefits (for example, 10GB of storage)
- [ ] Benefit Tickets
  - [ ] Create, edit, and delete benefit tickets
  - [ ] Define benefits and limits for each ticket
  - [ ] Pricing and discounts
- [ ] Usage and Limit Management
  - [ ] Track usage of benefits by subscribers
  - [ ] Enforce limits based on subscription plans
  - [ ] Provide usage analytics and reports
- [ ] Multi-tenancy
  - [ ] Support for multiple subscribers
  - [ ] Support for multiple subscription plans
  - [ ] Support for multiple benefit tickets 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lucas Vinicius](https://github.com/lucasdotvin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
