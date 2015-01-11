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
 * @copyright  Christian Schiffler <c.schiffler@cyberspectrum.de>, Tristan Lins <tristan@lins.io>
 * @link       https://github.com/phpcq/branch-alias-validation
 * @license    https://github.com/phpcq/branch-alias-validation/blob/master/LICENSE MIT
 * @filesource
 */

namespace PhpCodeQuality\BranchAliasValidation\Command;

use Bit3\GitPhp\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ValidateBranchAlias
 *
 * @package PhpCodeQuality\BranchAliasValidation\Command
 */
class ValidateBranchAlias extends Command
{
    /**
     * The current input interface.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The current output interface.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('phpcq:validate-branch-alias')
            ->setDescription('Validate that all branches are ahead of the most recent tag.')
            ->addArgument(
                'git-dir',
                InputArgument::OPTIONAL,
                'The directory where the git repository is located at.',
                '.'
            );
    }

    /**
     * Cut the "dev-" prefix from a branch.
     *
     * @param string $branch The branch to simplify.
     *
     * @return string
     */
    protected function simplifyBranch($branch)
    {
        return substr($branch, 4);
    }

    /**
     * Retrieve the latest tag from a branch.
     *
     * @param string $branch The branch name to retrieve the tag from.
     *
     * @return null|string Returns null when no tag has been found, the tag name otherwise.
     */
    protected function getTagFromBranch($branch)
    {
        $git  = new GitRepository($this->input->getArgument('git-dir'));
        $tag  = trim($git->describe()->tags()->always()->execute($branch));
        $hash = trim($git->revParse()->short(false)->execute($branch));
        return $hash !== $tag ? $tag : null;
    }

    /**
     * Validate the given branch.
     *
     * Returns true on success, the name of the offending tag on failure.
     *
     * @param string $tag   The tag to check.
     *
     * @param string $alias The alias for the given branch.
     *
     * @return bool|string
     */
    public function validate($tag, $alias)
    {
        $simpleAlias  = preg_replace('~(\.x)?-dev~', '', $alias);
        $versionLevel = count(explode('.', $simpleAlias));
        $reducedTag   = preg_replace('~-.*$~', '', $tag);
        $reducedTag   = implode('.', array_slice(explode('.', $reducedTag), 0, $versionLevel));

        return version_compare($reducedTag, $simpleAlias, '<=');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $exitCode = 0;
        $git      = new GitRepository($this->input->getArgument('git-dir'));
        $branches = $git->branch()->listBranches()->getNames();
        $composer = json_decode(file_get_contents($input->getArgument('git-dir') . '/composer.json'), true);

        if (!isset($composer['extra']['branch-alias'])) {
            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln('<info>No branch aliases found, skipping test.</info>');
            }
            return 0;
        }

        foreach ($composer['extra']['branch-alias'] as $branch => $alias) {
            $simpleBranch = $this->simplifyBranch($branch);
            if (!in_array($simpleBranch, $branches)) {
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(
                        sprintf(
                            '<info>Skipping non existing branch %s(%s)</info>',
                            $branch,
                            $alias
                        )
                    );
                }
                continue;
            }

            $tag = $this->getTagFromBranch($simpleBranch);
            // No tag yet, therefore definately before any version.
            if ($tag === null) {
                $output->writeln(
                    sprintf(
                        '<comment>Branch alias %s(%s) has not been tagged yet.</comment>',
                        $branch,
                        $alias
                    )
                );
            } elseif (!$this->validate($tag, $alias)) {
                $output->writeln(
                    sprintf(
                        '<error>The branch alias %s(%s) is behind the latest branch tag %s!</error>',
                        $branch,
                        $alias,
                        $tag
                    )
                );
                $exitCode = 1;
            } else {
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(
                        sprintf(
                            '<info>Branch alias %s(%s) is ahead of the latest branch tag.</info>',
                            $branch,
                            $alias
                        )
                    );
                }
            }
        }

        return $exitCode;
    }
}
