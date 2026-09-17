# Changelog

All notable changes to `webex` will be documented in this file

## v0.1.2 - 2026-09-17

### Added

* Use the configured room ID or recipient email as a fallback for notifications without an explicit recipient.

### Changed

* Reject ambiguous configuration when both default recipient values are set.

## v0.1.1 - 2026-09-17

### Changed

* Removed the unused `bot_id` configuration and channel argument.

## v0.1.0 - 2026-09-17

### Added

* Laravel 12 and 13 support.
* Publishable `config/webex.php` configuration.
* `webex:send` Artisan command for text, Markdown and single-file messages.

### Changed

* Renamed the package to `fabamb/laravel-webex`.
* Renamed the PHP namespace to `Fabamb\\LaravelWebex`.

The entries below are the upstream project history from
[`laravel-notification-channels/webex`](https://github.com/laravel-notification-channels/webex).

## v2.0.0 - 2024-04-15

### What's Changed

* Add support for Laravel 11 by @askmrsinh in https://github.com/laravel-notification-channels/webex/pull/11

**Full Changelog**: https://github.com/laravel-notification-channels/webex/compare/v1.0.1...v2.0.0

## v1.0.1 - 2024-03-06

### What's Changed

* Update README.md by @askmrsinh in https://github.com/laravel-notification-channels/webex/pull/9

**Full Changelog**: https://github.com/laravel-notification-channels/webex/compare/v1.0.0...v1.0.1

## v1.0.0 - 2024-03-05

- Initial Release
