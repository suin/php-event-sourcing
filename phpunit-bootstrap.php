<?php
// For composer
require_once 'vendor/autoload.php';

/**
 * You can overwrite SUIN_EVENT_SOURCING_TEST_DB_* by `export` command.
 * @see http://manpages.ubuntu.com/manpages/hardy/man5/exports.5.html
 *
 * Example:
 *
 * ```
 * export SUIN_EVENT_SOURCING_TEST_DB_USER=alice
 * export SUIN_EVENT_SOURCING_TEST_DB_PASS=passwd
 * phpunit
 * ```
 */
if ( isset($_SERVER['SUIN_EVENT_SOURCING_TEST_DB_HOST']) === false ) {
    $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_HOST'] = $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_HOST_DEFAULT'];
}
if ( isset($_SERVER['SUIN_EVENT_SOURCING_TEST_DB_NAME']) === false ) {
    $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_NAME'] = $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_NAME_DEFAULT'];
}
if ( isset($_SERVER['SUIN_EVENT_SOURCING_TEST_DB_USER']) === false ) {
    $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_USER'] = $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_USER_DEFAULT'];
}
if ( isset($_SERVER['SUIN_EVENT_SOURCING_TEST_DB_PASS']) === false ) {
    $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_PASS'] = $_SERVER['SUIN_EVENT_SOURCING_TEST_DB_PASS_DEFAULT'];
}
