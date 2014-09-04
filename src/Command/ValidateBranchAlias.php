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
namespace ContaoCommunityAlliance\BuildSystem\Tool\BranchAliasValidation\Command;

use ContaoCommunityAlliance\BuildSystem\Repository\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ValidateBranchAlias
 *
 * @package ContaoCommunityAlliance\BuildSystem\Tool\BranchAliasValidation\Command
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

    protected function configure()
    {
        $this
            ->setName('ccabs:tools:validate-branch-alias')
            ->setDescription('Validate that all branches are ahead of the most recent tag.')
            ->addArgument(
                'git-dir',
                InputArgument::OPTIONAL,
                'The directory where the git repository is located at.',
                '.'
            );
    }

    protected function simplifyBranch($branch)
    {
        return substr($branch, 4);
    }

    protected function getTagFromBranch($branch)
    {
        $git      = new GitRepository($this->input->getArgument('git-dir'));
        return trim($git->describe()->tags()->execute($branch));
    }

    /**
     * Validate the given branch.
     *
     * Returns true on success, the name of the offending tag on failure.
     *
     * @param  string $tag   The tag to check.
     *
     * @param  string $alias The alias for the given branch.
     *
     * @return bool|string
     */
    public function validate($tag, $alias)
    {
        $simpleAlias  = preg_replace("~(\.x)?-dev~", "", $alias);
        $versionLevel = count(explode('.', $simpleAlias));
        $reducedTag   = implode('.', array_slice(explode('.', $tag), 0, $versionLevel));
        return version_compare($reducedTag, $simpleAlias, "<=");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $git      = new GitRepository($this->input->getArgument('git-dir'));
        $branches = $git->branch()->listBranches()->getNames();
        $composer = json_decode(file_get_contents($input->getArgument('git-dir') . '/composer.json'), true);
        foreach ($composer["extra"]["branch-alias"] as $branch => $alias) {
            $simpleBranch = $this->simplifyBranch($branch);
            if (!in_array($simpleBranch, $branches)) {
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(
                        "<info>Skipping non existant branch $branch($alias).</info>"
                    );
                }
                continue;
            }

            $tag        = $this->getTagFromBranch($simpleBranch);
            if (!$this->validate($tag, $alias)) {
                $output->writeln(
                    "<error>The branch alias $branch($alias) is behind the latest branch tag $tag!</error>"
                );
                return 1;
            } else {
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $output->writeln(
                        "<info>Branch alias $branch($alias) is ahead of the latest branch tag.</info>"
                    );
                }
            }
        }

        return 0;
    }
}
