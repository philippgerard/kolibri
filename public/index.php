<?php

/**
 * Kolibri.
 *
 * This file handles all HTTP requests. Other files should not be
 * directly accessible for HTTP clients.
 *
 * @package   Kolibri
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */

define("APPLICATION_PATH", realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application'
) . DIRECTORY_SEPARATOR);

if (getenv('ENVIRONMENT') != 'production') {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

require_once(APPLICATION_PATH . "bootstrap.php");
