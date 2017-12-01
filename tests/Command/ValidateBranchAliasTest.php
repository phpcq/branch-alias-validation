<?php

/**
 * This file is part of phpcq/branch-alias-validation.
 *
 * (c) 2014 Christian Schiffler, Tristan Lins
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    phpcq/branch-alias-validation
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan@lins.io>
 * @copyright  2014-2016 Christian Schiffler <c.schiffler@cyberspectrum.de>, Tristan Lins <tristan@lins.io>
 * @license    https://github.com/phpcq/branch-alias-validation/blob/master/LICENSE MIT
 * @link       https://github.com/phpcq/branch-alias-validation
 * @filesource
 */

namespace PhpCodeQuality\BranchAliasValidation\Test\Command;

use PhpCodeQuality\BranchAliasValidation\Command\ValidateBranchAlias;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidateBranchAliasTest
 *
 * @package PhpCodeQuality\BranchAliasValidation\Test\Command
 */
class ValidateBranchAliasTest extends TestCase
{
    /**
     * Validate the given branch.
     *
     * @return void
     */
    public function testValidate()
    {
        $command = new ValidateBranchAlias();

        foreach (array
            (
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '2.0.x-dev',
                ),
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '3.0.x-dev',
                ),
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '2.x-dev',
                ),
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '3.x-dev',
                ),
                array(
                    'tag'   => '0.14-1-g9d5de05',
                    'alias' => '0.14.x-dev',
                ),
            ) as $entry
        ) {
            $this->assertTrue(
                $command->validate($entry['tag'], $entry['alias']),
                $entry['tag'] . '<=' . $entry['alias']
            );
        }

        foreach (array
            (
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '1.0.x-dev',
                ),
                array(
                    'tag'   => '3.0.0-beta20',
                    'alias' => '1.x-dev',
                ),
                array(
                    'tag'   => '2.0.0-beta20',
                    'alias' => '1.x-dev',
                ),
                array(
                    'tag'   => '4.0.0-beta20',
                    'alias' => '2.x-dev',
                ),
                 array(
                     'tag'   => '0.14-1-g9d5de05',
                     'alias' => '0.13.x-dev',
                 ),
            ) as $entry) {
            $this->assertFalse(
                $command->validate($entry['tag'], $entry['alias']),
                $entry['tag'] . ' <= ' . $entry['alias']
            );
        }
    }
}
