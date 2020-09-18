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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2014-2020 Christian Schiffler <c.schiffler@cyberspectrum.de>, Tristan Lins <tristan@lins.io>
 * @license    https://github.com/phpcq/branch-alias-validation/blob/master/LICENSE MIT
 * @link       https://github.com/phpcq/branch-alias-validation
 * @filesource
 */

namespace PhpCodeQuality\BranchAliasValidation\Test\Application;

use PhpCodeQuality\BranchAliasValidation\Application\ValidateBranchAliasApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * @covers \PhpCodeQuality\BranchAliasValidation\Application\ValidateBranchAliasApplication
 * @covers \PhpCodeQuality\BranchAliasValidation\Command\ValidateBranchAlias
 */
class ValidateBranchAliasApplicationTest extends TestCase
{
    public function testApplication()
    {
        $input = new ArrayInput(['--help' => '']);
        $output = new TestOutput();

        $application = new ValidateBranchAliasApplication();
        self::assertSame($application->doRun($input, $output), 0);
        self::assertNotEmpty($output->output);
        self::assertTrue($application->has('phpcq:validate-branch-alias'));
        $application->setAutoExit(false);
        self::assertFalse($application->isAutoExitEnabled());
        $application->setAutoExit(true);
        self::assertTrue($application->isAutoExitEnabled());
    }
}
