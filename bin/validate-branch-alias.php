#!/usr/bin/env php
<?php

/**
 * This file is part of phpcq/branch-alias-validation.
 *
 * (c) 2014-2020 Christian Schiffler, Tristan Lins
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    phpcq/branch-alias-validation
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan@lins.io>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2014-2020 Christian Schiffler <c.schiffler@cyberspectrum.de>, Tristan Lins <tristan@lins.io>
 * @license    https://github.com/phpcq/branch-alias-validation/blob/master/LICENSE MIT
 * @link       https://github.com/phpcq/branch-alias-validation
 * @filesource
 */

use PhpCodeQuality\BranchAliasValidation\Application\ValidateBranchAliasApplication;

error_reporting(E_ALL & ~ E_USER_DEPRECATED);

function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}
if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
    echo 'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL;
    exit(1);
}

set_error_handler(
    function ($errno, $errstr, $errfile, $errline ) {
        if (0 === ($errno & error_reporting())) {
            return;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
);

$application = new ValidateBranchAliasApplication();
$application->setAutoExit(true);
$application->run();
