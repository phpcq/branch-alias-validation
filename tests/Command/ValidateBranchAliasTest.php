<?php

/**
 * This file is part of the Contao Community Alliance Build System tools.
 *
 * @copyright 2014 Contao Community Alliance <https://c-c-a.org>
 * @author    Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package   contao-community-alliance/build-system-tool-branch-alias-validation
 * @license   MIT
 * @link      https://c-c-a.org
 */
namespace ContaoCommunityAlliance\BuildSystem\Tool\BranchAliasValidation\Test\Command;

use ContaoCommunityAlliance\BuildSystem\Tool\BranchAliasValidation\Command\ValidateBranchAlias;

/**
 * Class ValidateBranchAliasTest
 *
 * @package ContaoCommunityAlliance\BuildSystem\Tool\BranchAliasValidation\Test\Command
 */
class ValidateBranchAliasTest extends \PHPUnit_Framework_TestCase
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
            ) as $entry) {
            $this->assertFalse(
                $command->validate($entry['tag'], $entry['alias']),
                $entry['tag'] . ' <= ' . $entry['alias']
            );
        }
    }
}
