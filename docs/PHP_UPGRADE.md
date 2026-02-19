# PHP Upgrade Readiness

## Current Runtime
- PHP-FPM image: `php:7.4-fpm` in [docker/php/Dockerfile](file:///c:/inetpub/eproc/docker/php/Dockerfile)

## Optional PHP 8.2 Build
- Alternate Dockerfile: [docker/php/Dockerfile.php82](file:///c:/inetpub/eproc/docker/php/Dockerfile.php82)

## How to Try PHP 8.2 Locally
1. Create a local override file (do not commit secrets) that changes the PHP build to use `docker/php/Dockerfile.php82`.
2. Rebuild containers and run smoke tests (login page load, basic navigation, and existing PHPUnit smoke tests under `vms/app`).

## Expected Work Items Before Switching Defaults
- Replace legacy `mcrypt` usage with supported crypto (CI3 `Encrypt` library uses mcrypt-era patterns).
- Confirm strict typing/compatibility issues under PHP 8.x (deprecated features, warnings upgraded to errors).
- Run a baseline security pass (sessions/cookies, CSRF, uploads) and a regression test suite.
